<?php

use ArrayIterator\DataError;
use ArrayIterator\Helper\MimeTypes;
use ArrayIterator\Model\AbstractUserModel;
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
    $tableName = student()->getTableName();
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
        $userAvatar = get_avatar_uri($baseFile);
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
