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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Resolution;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResolutionController extends Controller
{
    /**
     * Display All Resolutions.
     */
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $resolutions = Resolution::all()->sortBy('position');

        return \view('Staff.resolution.index', ['resolutions' => $resolutions]);
    }

    /**
     * Show Resolution Create Form.
     */
    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        return \view('Staff.resolution.create');
    }

    /**
     * Store A New Resolution.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $resolution = new Resolution();
        $resolution->name = $request->input('name');
        $resolution->slug = Str::slug($resolution->name);
        $resolution->position = $request->input('position');

        $v = \validator($resolution->toArray(), [
            'name'     => 'required',
            'slug'     => 'required',
            'position' => 'required',
        ]);

        if ($v->fails()) {
            return \to_route('staff.resolutions.index')
                ->withErrors($v->errors());
        }

        $resolution->save();

        return \to_route('staff.resolutions.index')
                ->withSuccess('Resolution Successfully Added');
    }

    /**
     * Resolution Edit Form.
     */
    public function edit(int $id): \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    {
        $resolution = Resolution::findOrFail($id);

        return \view('Staff.resolution.edit', ['resolution' => $resolution]);
    }

    /**
     * Edit A Resolution.
     */
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $resolution = Resolution::findOrFail($id);
        $resolution->name = $request->input('name');
        $resolution->slug = Str::slug($resolution->name);
        $resolution->position = $request->input('position');

        $v = \validator($resolution->toArray(), [
            'name'     => 'required',
            'slug'     => 'required',
            'position' => 'required',
        ]);

        if ($v->fails()) {
            return \to_route('staff.resolutions.index')
                ->withErrors($v->errors());
        }

        $resolution->save();

        return \to_route('staff.resolutions.index')
                ->withSuccess('Resolution Successfully Modified');
    }

    /**
     * Delete A Resolution.
     *
     * @throws \Exception
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $resolution = Resolution::findOrFail($id);
        $resolution->delete();

        return \to_route('staff.resolutions.index')
            ->withSuccess('Resolution Successfully Deleted');
    }
}
