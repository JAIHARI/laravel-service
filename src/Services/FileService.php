<?php
namespace Czim\Service\Services;

use Czim\Service\Contracts\ServiceInterpreterInterface;
use Czim\Service\Contracts\ServiceRequestInterface;
use Czim\Service\Exceptions\CouldNotConnectException;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;

class FileService extends AbstractService
{

    /**
     * @var Filesystem
     */
    protected $files;


    /**
     * @param Filesystem                  $files
     * @param ServiceInterpreterInterface $interpreter
     * @param array                       $guzzleConfig default config to pass into the guzzle client
     */
    public function __construct(Filesystem $files = null, ServiceInterpreterInterface $interpreter = null, array $guzzleConfig = [])
    {
        if (is_null($files)) {
            $this->files = app(Filesystem::class);
        }

        parent::__construct(null, $interpreter);
    }

    /**
     * Performs raw REST call
     *
     * @param ServiceRequestInterface $request
     * @return mixed
     * @throws CouldNotConnectException
     * @throws Exception
     */
    protected function callRaw(ServiceRequestInterface $request)
    {
        $path = $this->makeFilePathFromRequest($request);

        try {

            $response = $this->files->get($path);

        } catch (FileNotFoundException $e) {

            throw new CouldNotConnectException($e->getMessage(), $e->getCode(), $e);
        }

        return $response;
    }

    /**
     * Builds an returns local file path from current request
     *
     * Path may be a combination of the location and the method
     * location may be the full file path and method may then be empty
     *
     * @param ServiceRequestInterface $request
     * @return string
     * @throws CouldNotConnectException
     */
    protected function makeFilePathFromRequest(ServiceRequestInterface $request)
    {
        $location = $request->getLocation();
        $method   = $request->getMethod();

        if ( ! empty($location) && ! empty($method)) {

            return rtrim($location(), '/') . '/' . $method();

        } elseif ( ! empty($location)) {

            return $location;

        } elseif ( ! empty($method)) {

            return $method;
        }

        throw new CouldNotConnectException('No path given for FileService. Set a location and/or method to build a valid path.');
    }

}