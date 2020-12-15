<?php

use ArrayIterator\DataError;
use ArrayIterator\Helper\MimeTypes;
use ArrayIterator\Model\AbstractUserModel;
use ArrayIterator\Model\Site;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @return array
 */
function allowed_attachment_types(): array
{
    $mimes = [
        'images' => [
            'jpeg',
            'jpg',
            'png',
            'webp',
            'ico',
            'gif',
            'svg',
        ],
        'document' => [
            'csv',
            'xls',
            'xlsx',
            'doc',
            'docx',
            'pdf',
            'ppt',
            'pptx',
        ],
        'text' => [
            'txt',
            'ini',
        ],
        'audio' => [
            'mp3',
            "m4a",
            'ogg',
            'wav',
            "wma"
        ],
        'video' => [
            'mp4',
            'mp4a',
            'mpeg',
            "mpg",
            "mpe",
            'mov',
        ]
    ];

    return hook_apply('allowed_attachment_types', $mimes);
}

/**
 * @return int
 */
function get_allowed_avatar_size(): int
{
    return hook_apply('allowed_avatar_size', MB_IN_BYTES);
}

function get_avatar_max_height(): int
{
    return hook_apply('avatar_max_height', 600);
}

function get_avatar_max_width(): int
{
    return hook_apply('avatar_max_width', 600);
}

function get_avatar_min_height(): int
{
    return hook_apply('avatar_min_height', 100);
}

function get_avatar_min_width(): int
{
    return hook_apply('avatar_min_width', 100);
}

/**
 * @param AbstractUserModel $model
 * @param string $name
 * @return DataError|false|string
 */
function upload_avatar(AbstractUserModel $model, string $name = 'avatar')
{
    if (!$model->isFromStatement()) {
        /**
         * @var AbstractUserModel $model
         */
        $model = $model->get();
    }

    if (!$model || !$model instanceof AbstractUserModel) {
        return new DataError(
            'avatar',
            trans('User is not exists')
        );
    }

    $siteId = $model->getSiteId();
    if (!is_int($siteId)) {
        return new DataError(
            'avatar',
            sprintf(trans('Site id %s is not exists'), $siteId)
        );
    }

    $userId = $model->getId();
    if (!is_int($userId)) {
        return new DataError(
            'avatar',
            sprintf(trans('User id %s is not exists'), $userId)
        );
    }

    $userAvatar = $model['avatar'] ?? null;
    $avatarPath = get_avatar_uploads_dir();
    $type = $model->getUserRoleType();
    $avatarDir = normalize_path(
        un_slash_it(
            sprintf(
                '%s/%s/%s',
                $avatarPath,
                $type,
                $siteId
            )
        )
    );
    if ($userAvatar) {
        if (strpos($userAvatar, '..') !== false) {
            $userAvatar = null;
        } else {
            $userAvatar = normalize_directory(sprintf('%s/%s', $avatarPath, $userAvatar));
        }
    }

    if (!is_dir($avatarDir)) {
        @mkdir($avatarDir, 0755, true);
    }

    if (!is_dir($avatarDir)) {
        return false;
    }

    if (!file_exists($avatarDir . '/index.php')) {
        @file_put_contents($avatarDir . '/index.php', "<?php\n// keep silent\n");
    }

    if (!file_exists(dirname($avatarDir) . '/index.php')) {
        @file_put_contents(dirname($avatarDir) . '/index.php', "<?php\n// keep silent\n");
    }
    if (!file_exists($avatarPath . '/index.php')) {
        @file_put_contents($avatarPath . '/index.php', "<?php\n// keep silent\n");
    }

    $file = get_uploaded_file($name);
    if (!$file instanceof UploadedFileInterface) {
        return new DataError(
            'avatar',
            trans('No avatar has been uploaded')
        );
    }
    if ($file->isMoved()) {
        return new DataError(
            'avatar',
            trans('Uploaded file is moved to another location')
        );
    }
    $mimes = allowed_attachment_types()['images'] ?? null;
    if (!is_array($mimes)) {
        return new DataError(
            'avatar',
            trans('No Images Mimetype allowed')
        );
    }

    if ($file->getSize() > get_allowed_avatar_size()) {
        return new DataError(
            'avatar',
            trans('Image size is bigger than maximum allowed')
        );
    }

    $mediaType = $file->getClientMediaType();
    $types = MimeTypes::fromMimeType($mediaType);
    if (empty($types)) {
        return new DataError(
            'avatar',
            sprintf(
                trans('Mimetype %s is invalid'),
                $mediaType
            )
        );
    }

    $types = reset($types);
    if (!is_string($types)) {
        return new DataError(
            'avatar',
            trans('No image extension defined')
        );
    }
    if (!in_array($types, $mimes)) {
        return new DataError(
            'avatar',
            sprintf(
                trans('Image extension %s is not allowed')
            )
        );
    }

    $name = $file->getClientFilename();
    $baseName = sprintf('%d-%s.%s', $userId, sha1(microtime() . $name), $types);
    $fileTarget = normalize_path(
        sprintf(
            '%s/%s',
            $avatarDir,
            $baseName
        )
    );

    $size = @getimagesizefromstring((string)$file->getStream());
    if (empty($size)) {
        return new DataError(
            'avatar',
            trans('Uploaded files maybe is not an image')
        );
    }
    $width = $size[0];
    $height = $size[1];
    $maxHeight = get_avatar_max_height();
    $maxWidth = get_avatar_max_width();
    $minHeight = get_avatar_min_height();
    $minWidth = get_avatar_min_width();

    if ($width > $maxWidth) {
        return new DataError(
            'avatar',
            trans('Avatar width more than recommended size')
        );
    }

    if ($height > $maxHeight) {
        return new DataError(
            'avatar',
            trans('Avatar height more than recommended size')
        );
    }
    if ($height < $minHeight) {
        return new DataError(
            'avatar',
            trans('Avatar height less than recommended size')
        );
    }
    if ($width < $minWidth) {
        return new DataError(
            'avatar',
            trans('Avatar width less than recommended size')
        );
    }

    $baseFile = '/' . ltrim(substr($fileTarget, strlen($avatarPath)), '/');
    $tableName = $model->getTableName();
    try {
        $file->moveTo($fileTarget);
    } catch (Throwable $e) {
        return new DataError(
            'avatar',
            $e->getMessage()
        );
    }

    if (!$file->isMoved() || !file_exists($fileTarget)) {
        return new DataError(
            'avatar',
            trans('Could not move uploaded file to avatar directory')
        );
    }

    try {
        $stmt = database_prepare(
            sprintf('UPDATE %s SET avatar=? WHERE id=?', $tableName)
        );
        $stmt->execute([$baseFile, $userId]);
        if ($userAvatar && is_file($userAvatar) && is_writable($userAvatar)) {
            unlink($userAvatar);
        }
        $userAvatar = get_avatar_url($baseFile);
        $data = [
            'url' => $userAvatar,
            'width' => $width,
            'height' => $height,
        ];
        cache_set(
            sprintf('%s(%d)', $model->getUserRoleType(), $model->getId()),
            $data,
            'avatars'
        );
        return $userAvatar;
    } catch (Throwable $e) {
        if (file_exists($fileTarget)) {
            unlink($fileTarget);
        }
        return new DataError('avatar', $e->getMessage());
    }
}

/**
 * @param int $userId
 * @param string $name
 * @return DataError|false|string
 */
function upload_student_avatar(int $userId, string $name = 'avatar')
{
    $user = get_student_by_id($userId);
    if (!$user) {
        return new DataError(
            'avatar',
            sprintf(trans('User id %s is not exists'), $userId)
        );
    }

    return upload_avatar($user, $name);
}

/**
 * @param int $userId
 * @param string $name
 * @return DataError|false|string
 */
function upload_supervisor_avatar(int $userId, string $name = 'avatar')
{
    $user = get_supervisor_by_id($userId);
    if (!$user) {
        return new DataError(
            'avatar',
            sprintf(trans('User id %s is not exists'), $userId)
        );
    }

    return upload_avatar($user, $name);
}

/**
 * @param AbstractUserModel $model
 * @return false|array
 */
function get_user_avatar(AbstractUserModel $model)
{
    if ($model->isFromStatement()) {
        $newModel = $model->get();
        if ($newModel) {
            $model = $newModel;
        }
    }
    $key = sprintf('%s(%d)', $model->getUserRoleType(), $model->getId());
    $cache = cache_get($key, 'avatars', $found);
    if ($found && (!is_array($cache) || $cache === false)) {
        return $cache;
    }
    cache_set($key, false, 'avatars');
    $avatar = $model->get('avatar');
    if (!is_string($avatar) || trim($avatar) === '') {
        return false;
    }
    $path = normalize_directory(get_avatar_uploads_dir().'/'.$avatar);
    if (!file_exists($path)) {
        return false;
    }
    $size = @getimagesize($path);
    if (!$size) {
        return false;
    }
    $width = $size[0];
    $height = $size[1];
    $data = [
        'url' => get_avatar_url($avatar),
        'width' => $width,
        'height' => $height,
    ];
    cache_set($key, $data, 'avatars');
    return $data;
}

/**
 * @param int|null $userId
 * @return array|false
 */
function get_supervisor_avatar(int $userId = null)
{
    if (!$userId) {
        $supervisor = get_current_supervisor();
        if (!$supervisor) {
            return false;
        }
        $userId = $supervisor->getId();
    }

    if (!is_int($userId)) {
        return false;
    }

    $user = get_supervisor_by_id($userId);
    if (!$user) {
        return false;
    }
    return get_user_avatar($user);
}

/**
 * @param int|null $userId
 * @return array|false
 */
function get_student_avatar(int $userId = null)
{
    if (!$userId) {
        $student = get_current_student();
        if (!$student) {
            return false;
        }
        $userId = $student->getId();
    }

    if (!is_int($userId)) {
        return false;
    }

    $user = get_student_by_id($userId);
    if (!$user) {
        return false;
    }
    return get_user_avatar($user);
}

/*! LOGO */

/**
 * @return int
 */
function get_allowed_logo_size(): int
{
    return hook_apply('allowed_logo_size', MB_IN_BYTES);
}

function get_logo_max_height(): int
{
    return hook_apply('logo_max_height', 600);
}

function get_logo_max_width(): int
{
    return hook_apply('logo_max_width', 600);
}

function get_logo_min_height(): int
{
    return hook_apply('logo_min_height', 100);
}

function get_logo_min_width(): int
{
    return hook_apply('logo_min_width', 100);
}

/**
 * @param string $name
 * @param int|null $siteId
 * @return DataError|false|string
 */
function upload_logo(string $name = 'logo', int $siteId = null)
{
    $siteId = $siteId??get_current_site_id();
    $site = get_site_by_id($siteId);
    if (!$site instanceof Site) {
        return new DataError(
            'logo',
            sprintf(trans('Site id %s is not exists'), $siteId)
        );
    }

    $siteLogo = $model['logo'] ?? null;
    $siteLogo = trim($siteLogo) === '' ?? null;
    $logoDir = get_logo_uploads_dir();
    if (is_string($siteLogo)) {
        if (!$siteLogo || strpos($siteLogo, '..') !== false) {
            $siteLogo = null;
        } else {
            $siteLogo = normalize_directory(sprintf('%s/%s', $logoDir, $siteLogo));
        }
    }

    if (!is_dir($logoDir)) {
        @mkdir($logoDir, 0755, true);
    }

    if (!is_dir($logoDir)) {
        return false;
    }

    if (!file_exists($logoDir . '/index.php')) {
        @file_put_contents($logoDir . '/index.php', "<?php\n// keep silent\n");
    }

    if (!file_exists(dirname($logoDir) . '/index.php')) {
        @file_put_contents(dirname($logoDir) . '/index.php', "<?php\n// keep silent\n");
    }

    $file = get_uploaded_file($name);
    if (!$file instanceof UploadedFileInterface) {
        return new DataError(
            'logo',
            trans('No logo has been uploaded')
        );
    }
    if ($file->isMoved()) {
        return new DataError(
            'logo',
            trans('Uploaded file is moved to another location')
        );
    }
    $mimes = allowed_attachment_types()['images'] ?? null;
    if (!is_array($mimes)) {
        return new DataError(
            'logo',
            trans('No Images Mimetype allowed')
        );
    }

    if ($file->getSize() > get_allowed_avatar_size()) {
        return new DataError(
            'logo',
            trans('Image size is bigger than maximum allowed')
        );
    }

    $mediaType = $file->getClientMediaType();
    $types = MimeTypes::fromMimeType($mediaType);
    if (empty($types)) {
        return new DataError(
            'avatar',
            sprintf(
                trans('Mimetype %s is invalid'),
                $mediaType
            )
        );
    }

    $types = reset($types);
    if (!is_string($types)) {
        return new DataError(
            'avatar',
            trans('No image extension defined')
        );
    }
    if (!in_array($types, $mimes)) {
        return new DataError(
            'avatar',
            sprintf(
                trans('Image extension %s is not allowed')
            )
        );
    }

    $name = $file->getClientFilename();
    $baseName = sprintf('%d-%s.%s', $siteId, sha1(microtime() . $name), $types);
    $fileTarget = normalize_path(
        sprintf(
            '%s/%s',
            $logoDir,
            $baseName
        )
    );

    $size = @getimagesizefromstring((string)$file->getStream());
    if (empty($size)) {
        return new DataError(
            'avatar',
            trans('Uploaded files maybe is not an image')
        );
    }
    $width = $size[0];
    $height = $size[1];
    $maxHeight = get_logo_max_height();
    $maxWidth = get_logo_max_width();
    $minHeight = get_logo_min_height();
    $minWidth = get_logo_min_width();

    if ($width > $maxWidth) {
        return new DataError(
            'logo',
            trans('Avatar width more than recommended size')
        );
    }

    if ($height > $maxHeight) {
        return new DataError(
            'logo',
            trans('Avatar height more than recommended size')
        );
    }
    if ($height < $minHeight) {
        return new DataError(
            'logo',
            trans('Avatar height less than recommended size')
        );
    }
    if ($width < $minWidth) {
        return new DataError(
            'logo',
            trans('Avatar width less than recommended size')
        );
    }

    $baseFile = '/' . ltrim($baseName, '/');
    $tableName = $site->getTableName();
    try {
        $file->moveTo($fileTarget);
    } catch (Throwable $e) {
        return new DataError(
            'avatar',
            $e->getMessage()
        );
    }

    if (!$file->isMoved() || !file_exists($fileTarget)) {
        return new DataError(
            'avatar',
            trans('Could not move uploaded file to avatar directory')
        );
    }

    try {
        $stmt = database_prepare(
            sprintf('UPDATE %s SET logo=? WHERE id=?', $tableName)
        );
        $stmt->execute([$baseFile, $siteId]);
        if ($siteLogo && is_file($siteLogo) && is_writable($siteLogo)) {
            unlink($siteLogo);
        }
        $siteLogo = get_logo_url($baseFile);
        $data = [
            'url' => $siteLogo,
            'width' => $width,
            'height' => $height,
        ];
        cache_set($siteId, $data, 'site_logo');
        return $siteLogo;
    } catch (Throwable $e) {
        if (file_exists($fileTarget)) {
            unlink($fileTarget);
        }
        return new DataError('logo', $e->getMessage());
    }
}

/**
 * @param int|null $siteId
 * @return array|false
 */
function get_site_logo(int $siteId = null)
{
    $siteId = $siteId??get_current_site_id();
    $site = get_site_by_id($siteId);
    if (!$site) {
        return false;
    }
    $cache = cache_get($siteId, 'site_logo', $found);
    if ($found && (is_array($cache) || $cache === false)) {
        return $cache;
    }
    cache_set($siteId, false, 'site_logo');

    $logo = $site->get('logo');
    if (!$logo || !is_string($logo)) {
        return false;
    }
    $path = normalize_directory(get_logo_uploads_dir().'/'.$logo);
    if (!file_exists($path)) {
        return false;
    }

    $size = @getimagesize($path);
    if (!$size) {
        return false;
    }
    $width = $size[0];
    $height = $size[1];
    $data = [
        'url' => get_logo_url($logo),
        'width' => $width,
        'height' => $height,
    ];
    cache_set($siteId, $data, 'site_logo');
    return $data;
}
