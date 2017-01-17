<?php namespace App\Http\Controllers\Lite\PublishedFiles;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Lite\LiteController;
use App\Lite\Services\PublishedFiles\PublishedFilesService;

/**
 * Class PublishedFilesController
 * @package App\Http\Controllers\Lite\PublishedFiles
 */
class PublishedFilesController extends LiteController
{
    /**
     * @var PublishedFilesService
     */
    protected $publishedFilesService;

    /**
     * PublishedFilesController constructor.
     * @param PublishedFilesService $publishedFilesService
     */
    public function __construct(PublishedFilesService $publishedFilesService)
    {
        $this->middleware('auth');
        $this->publishedFilesService = $publishedFilesService;
    }

    /**
     * List all the published files for the current Organization.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $publishedFiles = $this->publishedFilesService->all();

        return view('lite.publishedFiles.index', compact('publishedFiles'));
    }

    /**
     * Delete an XML file.
     *
     * @param         $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $file = $this->getActivityPublishedFile($id);

        if (Gate::denies('ownership', $file)) {
            return redirect()->route('lite.activity.index')->withResponse($this->getNoPrivilegesMessage());
        }

        $this->authorize('delete_activity', $file);

        if (!$this->publishedFilesService->delete($id)) {
            $response = ['type' => 'danger', 'code' => ['message', ['message' => trans('lite/global.delete_unsuccessful', ['type' => 'File'])]]];
        } else {
            $response = ['type' => 'success', 'code' => ['message', ['message' => trans('lite/global.deleted_successfully', ['type' => 'File'])]]];
        }

        return redirect()->back()->withResponse($response);
    }

    /**
     * Publish multiple files at once.
     *
     * @param Request $request
     * @return mixed
     */
    public function bulkPublish(Request $request)
    {
        if (!$this->publishedFilesService->publish($request->except('_token'))) {
            return redirect()->back()->withResponse(['type' => 'danger', 'code' => ['message', ['message' => trans('lite/global.delete_unsuccessful', ['type' => 'File'])]]]);
        }

        return redirect()->back()->withResponse([]);
    }
}
