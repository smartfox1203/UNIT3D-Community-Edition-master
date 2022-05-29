<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;

/**
 * @see \Tests\Todo\Feature\Http\Controllers\ImageControllerTest
 */
class ImageController extends Controller
{
    /**
     * Show Image Create Form.
     */
    public function create(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $album = Album::find($id);

        return \view('album.image', ['album' => $album]);
    }

    /**
     * Store A New Image.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $image = new Image();
        $image->user_id = $request->user()->id;
        $image->album_id = $request->input('album_id');
        $image->description = $request->input('description');
        $image->type = $request->input('type');

        $file = $request->file('image');
        $randomName = \uniqid('', true);
        $destinationPath = \public_path('/files/img/');
        $clientOriginalExtension = $file->getClientOriginalExtension();
        $filename = 'album-image_'.$randomName.'.'.$clientOriginalExtension;
        $uploadSuccess = $request->file('image')->move($destinationPath, $filename);
        $image->image = $filename;

        $v = \validator($image->toArray(), [
            'album_id'    => 'required|numeric|exists:albums,id',
            'user_id'     => 'required',
            'description' => 'required',
            'type'        => 'required',
            'image'       => 'required',
        ]);

        if ($v->fails()) {
            return \to_route('images.create', ['id' => $request->input('album_id')])
                ->withErrors($v->errors());
        }

        $image->save();

        return \to_route('albums.show', ['id' => $request->input('album_id')])
            ->withSuccess(\trans('gallery.image-published-success'));
    }

    /**
     * Download A Image.
     */
    public function download(int $id): \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $image = Image::findOrFail($id);
        $filename = $image->image;

        if (! \file_exists(\getcwd().'/files/img/'.$filename)) {
            return \to_route('show_album', ['id' => $image->album_id])
                ->withErrors(\trans('gallery.image-album-not-found'));
        }

        $image->downloads++;
        $image->save();

        return \response()->download(\getcwd().'/files/img/'.$filename);
    }

    /**
     * Delete A Image.
     *
     * @throws \Exception
     */
    public function destroy(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $image = Image::findOrFail($id);

        \abort_unless($user->group->is_modo || $user->id === $image->user_id, 403);
        $image->delete();

        return \to_route('albums.show', ['id' => $image->album_id])
            ->withSuccess(\trans('gallery.image-album-deleted-success'));
    }
}
