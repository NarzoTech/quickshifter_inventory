<?php

namespace Modules\GlobalSetting\app\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomAddon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Modules\GlobalSetting\app\Traits\ArchiveHelperTrait;
use ZipArchive;
use Nwidart\Modules\Facades\Module;

class ManageAddonController extends Controller
{
    use ArchiveHelperTrait;

    public function index()
    {
        $addons = CustomAddon::latest()->get();
        return view('globalsetting::addons.manage_addon', ['addons' => $addons]);
    }

    public function installAddon()
    {
        $files = glob(public_path('addons_files') . "/*");

        $addonFiles = [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'zip' && $this->isFirstDirAddons($file)) {
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $fileExtension = pathinfo($file, PATHINFO_EXTENSION);
                $addonFiles[$fileName . '.' . $fileExtension] = $this->checkAndReadJsonFile($file);
            }
        }

        return view('globalsetting::addons.install_addon', ['addonFiles' => $addonFiles]);
    }


    public function installStore(Request $request)
    {
        $request->validate([
            'zip_file' => 'required|mimes:zip',
        ]);

        $zipFilePath = public_path('addons_files/addon.zip');

        if (File::exists($zipFilePath)) {
            File::delete($zipFilePath);
        }

        // Store the uploaded file
        $zipFile = $request->file('zip_file');
        $zipFilePath = $zipFile->move(public_path('addons_files'), 'addon.zip');

        if (!$this->isFirstDirAddons($zipFilePath) && !$this->checkAndReadJsonFile($zipFilePath)) {
            File::delete($zipFilePath);
            $notification = __('Invalid Addon File Structure');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->with($notification);
        }

        $notification = __('Uploaded Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];
        return back()->with($notification);
    }


    public function installProcessStart()
    {
        $zipFilePath = public_path('addons_files/addon.zip');

        if (!File::exists($zipFilePath)) {
            $notification = __('No Addon File Found!');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->with($notification);
        }

        if (!$this->isFirstDirAddons($zipFilePath)) {
            $notification = __('Invalid Addon File Structure');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return redirect()->back()->with($notification);
        }

        $file = $zipFilePath;
        if (pathinfo($file, PATHINFO_EXTENSION) === 'zip' && $this->isFirstDirAddons($file)) {
            $addonFile = $this->checkAndReadJsonFile($file);
            $addonFileJson = json_decode(json_encode($addonFile), true);

            $addonExist = CustomAddon::where('name', $addonFile->name)->first();

            if($addonExist && count($addonFileJson) > 0 && ($addonFile?->version == $addonExist?->version)){
                $notification = __('Addon Already Installed');
                $notification = ['messege' => $notification, 'alert-type' => 'error'];
                return redirect()->back()->with($notification);
            }

            try {
                $zip = new ZipArchive;
                if ($zip->open($zipFilePath) === true) {
                    $zip->extractTo(base_path());
                    $zip->close();
                } else {
                    $notification = __('Corrupted Zip File');
                    $notification = ['messege' => $notification, 'alert-type' => 'error'];
                    return redirect()->back()->with($notification);
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
                $notification = __('Corrupted Zip File');
                $notification = ['messege' => $notification, 'alert-type' => 'error'];
                return redirect()->back()->with($notification);
            }

            $getModuleJson = $this->checkAndReadJsonFile($file, 'module.json');
            $customAddon = new CustomAddon();
            $customAddon->slug = $getModuleJson->name;
            foreach ($addonFileJson as $key => $value) {
                $customAddon->$key = is_array($value) ? json_encode($value) : $value;
            }
            $customAddon->status = 0;
            $customAddon->save();

            Module::register($customAddon->slug);

            unlink($zipFilePath);
        }
        $notification = __('Installed Successfully!');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];
        return to_route('admin.addons.view')->with($notification);
    }

    public function updateStatus($slug)
    {
        $addon = CustomAddon::whereSlug($slug)->firstOrFail();

        $status = $addon->status == 1 ? 0 : 1;

        Module::scan();

        if (!Module::has($addon->slug)) {
            $notification = __('Addon Not Found');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return back()->with($notification);
        }

        if ($status) {
            Module::enable($addon->slug);
            $module = Module::find($addon->slug);
            if ($module->isEnabled()) {
                $addon->status = $status;
                // write code to inject the code into the sidebarfile
                $sidebarFilePath = base_path('resources/views/admin/addons.blade.php');
                $sidebarFileContent = File::get($sidebarFilePath);
                $injectedCode = "\n@includeIf('" . $module->getLowerName() . "::sidebar')";
                if (strpos($sidebarFileContent, $injectedCode) === false) {
                    // Add the injected code
                    $updatedSidebarFileContent = str_replace('<!-- Addon:Sidebar -->', '<!-- Addon:Sidebar -->' . $injectedCode, $sidebarFileContent);

                    // Write the updated content to the file
                    File::put($sidebarFilePath, $updatedSidebarFileContent);
                }
            }
        } else {
            $module = Module::find($addon->slug);
            $module->disable();
            if ($module->isDisabled()) {
                $addon->status = $status;
            }
        }

        $addon->save();

        $notification = __('Updated Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];
        return back()->with($notification);
    }

    public function uninstallAddon($slug)
    {
        $addon = CustomAddon::whereSlug($slug)->firstOrFail();

        Module::scan();
        $module = Module::find($addon->slug);

        if (!Module::has($addon->slug)) {
            $notification = __('Addon Not Found');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];
            return back()->with($notification);
        }

        if ($module->delete()) {
            $addon->delete();
            // write code to remove the code from the sidebarfile
            $sidebarFilePath = base_path('resources/views/admin/addons.blade.php');
            $sidebarFileContent = File::get($sidebarFilePath);
            $injectedCode = "\n@includeIf('" . $module->getLowerName() . "::sidebar')";
            if (strpos($sidebarFileContent, $injectedCode)) {
                $updatedSidebarFileContent = str_replace($injectedCode, '', $sidebarFileContent);
                File::put($sidebarFilePath, $updatedSidebarFileContent);
            }
        }


        $notification = __('Deleted Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];
        return back()->with($notification);
    }

    public function deleteAddon()
    {
        $this->deleteFolderAndFiles(public_path('addons_files'));

        $notification = __('Deleted Successfully');
        $notification = ['messege' => $notification, 'alert-type' => 'success'];
        return back()->with($notification);
    }
}
