<?php
namespace Zento\ThemeManager\View\Finders;

use App;
use Registry;

use Illuminate\View\FileViewFinder;
use Illuminate\View\ViewFinderInterface;
use Illuminate\Support\Str;

class S3ViewFinder {
    const PATH_DELIMITER = '.';

    protected $s3;

    private $bucketName;
    private $themeName;

    public function __construct(string $bucketName, string $themeName) {
        $this->bucketName = $bucketName;
        $this->themeName = $themeName;
    }

    protected function getS3() {
        if (empty($this->s3)) {
            $this->s3 = App::make('aws')->createClient('s3');
        }
        return $this->s3;
    }

    /**
     * Get the fully qualified location of the view.
     *
     * @param  string  $name
     * @return string
     */
    public function find($name, Manager $manager) {
        $path = $manager->getCacheFileName('s3', $name);
        if (!\file_exists($path)) {
            $reqName = str_replace('.', '/', $name);
            $reqName = empty($this->themeName) ? sprintf('%s.blade.php', $reqName) : sprintf('%s/%s.blade.php', $this->themeName, $reqName);
            try {
                $object = $this->getS3()->getObject(array(
                    'Bucket'     => $this->bucketName, //'larval-themes',
                    'Key'        => $reqName,
                    'SaveAs'    => $path,
                ));
                // print_r($object->get('ETag'));die;
            } catch (\Aws\S3\Exception\S3Exception $e) {
                unlink($path);
                return null;
            }
        }
    
        return $path;
    }
}