<?php
namespace Czim\Service\Interpreters\Decorators;

use Czim\Service\Contracts\ServiceInterpreterInterface;
use Czim\Service\Contracts\ServiceRequestInterface;
use Czim\Service\Contracts\ServiceResponseInformationInterface;
use Czim\Service\Contracts\ServiceResponseInterface;
use Czim\Service\Exceptions\CouldNotInterpretResponse;

/**
 * Validates raw response before interpretation
 */
abstract class AbstractValidationPreDecorator implements ServiceInterpreterInterface
{

    /**
     * @var ServiceInterpreterInterface
     */
    protected $interpreter;

    /**
     * Validation errors
     *
     * @var array
     */
    protected $errors = [];


    /**
     * @param ServiceInterpreterInterface $interpreter
     */
    public function __construct(ServiceInterpreterInterface $interpreter)
    {
        $this->interpreter = $interpreter;
    }


    /**
     * @param ServiceRequestInterface             $request the request sent in order to retrieve the response
     * @param mixed                               $response
     * @param ServiceResponseInformationInterface $responseInformation
     * @return ServiceResponseInterface
     */
    public function interpret(ServiceRequestInterface $request, $response, ServiceResponseInformationInterface $responseInformation = null)
    {
        if ( ! $this->validate($response)) {
            $this->throwValidationException();
        }

        return $this->interpreter->interpret($request, $response, $responseInformation);
    }

    /**
     * Validates the (raw) response
     *
     * @param mixed $response
     * @return bool
     */
    abstract protected function validate($response);


    /**
     * Throws an exception, indicating that validation failed
     *
     * @throws CouldNotInterpretResponse
     */
    protected function throwValidationException()
    {
        throw new CouldNotInterpretResponse( $this->getErrorMessage() );
    }

    /**
     * Returns exception message for failed validation
     *
     * @return string
     */
    protected function getErrorMessage()
    {
        return print_r($this->getErrors(), true);
    }

    /**
     * Returns validation errors for previous attempt
     *
     * @return array
     */
    protected function getErrors()
    {
        return $this->errors;
    }

}
