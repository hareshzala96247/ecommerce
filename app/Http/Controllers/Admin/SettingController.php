<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        return response()->json(Setting::allKeyed());
    }

    public function publicIndex()
    {
        $allowed = ['site_name','site_tagline','logo','favicon','contact_email','contact_phone','contact_address','social_facebook','social_instagram','social_twitter','social_youtube','meta_title','meta_description'];
        $all = Setting::allKeyed();
        $data = array_intersect_key($all, array_flip($allowed));

        // Fall back to Laravel config/app.php name if not set in admin
        if (empty($data['site_name'])) {
            $data['site_name'] = config('app.name');
        }

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name'        => 'nullable|string|max:100',
            'site_tagline'     => 'nullable|string|max:200',
            'contact_email'    => 'nullable|email|max:100',
            'contact_phone'    => 'nullable|string|max:30',
            'contact_address'  => 'nullable|string|max:300',
            'social_facebook'  => 'nullable|url|max:200',
            'social_instagram' => 'nullable|url|max:200',
            'social_twitter'   => 'nullable|url|max:200',
            'social_youtube'   => 'nullable|url|max:200',
            'meta_title'       => 'nullable|string|max:150',
            'meta_description' => 'nullable|string|max:300',
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:1024',
            'favicon'          => 'nullable|image|mimes:ico,png,svg|max:256',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
            if (!in_array(mime_content_type($file->getRealPath()), $allowed, true)) {
                abort(422, 'Invalid logo file.');
            }
            $old = Setting::get('logo');
            if ($old) Storage::disk('public')->delete($old);
            $data['logo'] = $file->store('settings', 'public');
        } else {
            unset($data['logo']);
        }

        if ($request->hasFile('favicon')) {
            $file = $request->file('favicon');
            $allowed = ['image/png', 'image/svg+xml', 'image/x-icon', 'image/vnd.microsoft.icon'];
            if (!in_array(mime_content_type($file->getRealPath()), $allowed, true)) {
                abort(422, 'Invalid favicon file.');
            }
            $old = Setting::get('favicon');
            if ($old) Storage::disk('public')->delete($old);
            $data['favicon'] = $file->store('settings', 'public');
        } else {
            unset($data['favicon']);
        }

        $allowedKeys = [
            'site_name', 'site_tagline', 'contact_email', 'contact_phone', 'contact_address',
            'social_facebook', 'social_instagram', 'social_twitter', 'social_youtube',
            'meta_title', 'meta_description', 'logo', 'favicon',
        ];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys, true)) {
                Setting::set($key, $value ?? '');
            }
        }

        return response()->json(['message' => 'Settings saved.', 'settings' => Setting::allKeyed()]);
    }
}
