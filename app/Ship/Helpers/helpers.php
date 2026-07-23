<?php

use App\Containers\AppSection\User\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

if (!function_exists('get_auth')) {
    /**
     * Lấy thông tin tài khoản đăng nhập
     * 
     * @return User
     */
    function get_auth()
    {
        return Auth::user();
    }
}

if (!function_exists('storage_url')) {
    /**
     * Lấy liên kết tập tin
     * 
     * @param string $path
     * @param string|null $disk
     * 
     * @return string
     */
    function storage_url($path, $disk = null)
    {
        if (!is_string($path) or !$path) {
            return $path;
        }

        if ($disk) {
            /** @var FilesystemAdapter $storage */
            $storage = Storage::disk($disk);

            return $storage->url($path);
        }

        return Storage::url($path);
    }
}

if (!function_exists('cdn_path')) {
    /**
     * Lấy đường dẫn file của cdn url
     * 
     * @param array|string $urls
     * 
     * @return array|string
     */
    function cdn_path($urls)
    {
        $cdn = config('filesystems.disks.cdn');
        $host = $cdn['url'] . '/' . $cdn['dir'] . '/';

        if (is_array($urls)) {
            return array_map(fn($url) => str_replace($host, '', $url), $urls);
        }

        return str_replace($host, '', $urls);
    }
}

if (!function_exists('cdn_url')) {
    /**
     * Lấy url đầy đủ của cdn
     * 
     * @param array|string $paths
     * 
     * @return array|string
     */
    function cdn_url($paths)
    {
        if (is_array($paths)) {
            return array_map(fn($path) => storage_url($path, 'cdn'), $paths);
        }

        return storage_url($paths, 'cdn');
    }
}

if (!function_exists('cdn_upload')) {
    /**
     * Upload file lên cdn
     * 
     * @param UploadedFile $file
     * @param string $dir
     * 
     * @return string
     */
    function cdn_upload($file, $dir = '')
    {
        $ext = $file->getClientOriginalExtension();
        $isImage = strpos($file->getMimeType(), 'image/') === 0;
        $prefix = $isImage ? 'img-' : 'file-';
        $filename = $prefix . time() . '-' . str()->uuid() . '.' . $ext;

        $path = $file->storeAs($dir, $filename, 'cdn');

        return cdn_url($path);
    }
}

if (!function_exists('cdn_delete')) {
    /**
     * Xóa cdn url
     * 
     * @param array|string $paths
     * 
     * @return void
     */
    function cdn_delete($paths)
    {
        $paths = cdn_path($paths);
        Storage::disk('cdn')->delete($paths);
    }
}

if (!function_exists('generate_avatar')) {
    /**
     * Tạo ảnh đại diện bằng tên
     * 
     * @param string $name
     * 
     * @return string
     */
    function generate_avatar(string $name)
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));
        $parts = explode(' ', $name);
        $len = count($parts);
        if ($len == 1) {
            $initials = mb_strtoupper(mb_substr($parts[0], 0, 1));
        } else {
            $first = mb_substr($parts[$len - 2], 0, 1);
            $last = mb_substr($parts[$len - 1], 0, 1);
            $initials = mb_strtoupper($first . $last);
        }

        $size = 128;
        $radius = $size / 2;
        $hash = crc32(mb_strtolower($name));
        $fontSize = (strlen($initials) > 1) ? intval($size * 0.45) : intval($size * 0.60);

        $r = ($hash & 0xFF0000) >> 16;
        $g = ($hash & 0x00FF00) >> 8;
        $b = ($hash & 0x0000FF);

        $brightness = (0.299 * $r + 0.587 * $g + 0.114 * $b);
        if ($brightness > 200) {
            $r = max(0, $r - 80);
            $g = max(0, $g - 80);
            $b = max(0, $b - 80);
        }

        $bgColor = sprintf('#%02X%02X%02X', $r, $g, $b);

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . $size . ' ' . $size . '">';
        $svg .= '<circle cx="' . $radius . '" cy="' . $radius . '" r="' . $radius . '" fill="' . $bgColor . '"/>';
        $svg .= '<text x="50%" y="50%" dy=".35em" text-anchor="middle" font-family="Arial, sans-serif" font-weight="600" font-size="' . $fontSize . 'px" fill="#ffffff">';
        $svg .= htmlspecialchars($initials, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $svg .= '</text></svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }
}

if (!function_exists('get_include')) {
    /**
     * Lấy tham số include từ request
     * 
     * @return array
     */
    function get_include()
    {
        if (!request()->filled('include')) {
            return [];
        }

        $includes = explode(',', request()->get('include', ''));
        $includes = array_map('trim', $includes);

        return $includes;
    }
}

if (!function_exists('has_include')) {
    /**
     * Kiểm tra include tồn tại trong request
     * 
     * @param string $name
     * 
     * @return bool
     */
    function has_include($name)
    {
        $includes = get_include();
        if (empty($includes)) {
            return false;
        }

        $exists = Arr::first($includes, fn($include) => $include == $name or in_array($name, explode('.', $include)));

        return $exists;
    }
}

if (!function_exists('parse_statuses')) {
    /**
     * Chuyển đổi danh sách trạng thái
     * 
     * @param array $statuses
     * 
     * @return array
     */
    function parse_statuses($statuses)
    {
        $arr = [];

        if (!empty($statuses['active'])) {
            $arr = array_merge($arr, $statuses['active']);
        }

        if (!empty($statuses['pending'])) {
            $arr = array_merge($arr, $statuses['pending']);
        }

        $arr[] = $statuses['done'];

        return $arr;
    }
}

if (!function_exists('get_urls_from_content')) {
    /**
     * Lấy danh sách urls trong nội dung
     * 
     * @param string $content
     * 
     * @return array
     */
    function get_urls_from_content($content)
    {
        if (!$content) {
            return [];
        }

        preg_match_all(
            '#https?://\S+#',
            $content,
            $matches
        );

        if (!empty($matches[0])) {
            return $matches[0];
        }

        return [];
    }
}
