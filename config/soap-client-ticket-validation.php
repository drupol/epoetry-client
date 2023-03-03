<?php

use Phpro\SoapClient\CodeGenerator\Assembler;
use Phpro\SoapClient\CodeGenerator\Rules;
use Phpro\SoapClient\CodeGenerator\Config\Config;
use Soap\ExtSoapEngine\ExtSoapOptions;
use Phpro\SoapClient\Soap\DefaultEngineFactory;

// Generate SOAP client library to perform EU Login ticket validation.
// @link https://citnet.tech.ec.europa.eu/CITnet/confluence/display/IAM/EU+Login+Endpoints#EULoginEndpoints-CASTicketValidationWSDL
// WSDL used for generating the SOAP codebase can be found here:
// @link https://ecas.ec.europa.eu/cas/ws/TicketValidationService?WSDL
// The content returned by the URL above is stored locally in resources/ticket-validation.wsdl.
return Config::create()
    ->setEngine($engine = DefaultEngineFactory::create(
        ExtSoapOptions::defaults(__DIR__.'/../resources/ticket-validation.wsdl', [])
            ->disableWsdlCache()
    ))
    ->setTypeDestination('src/TicketValidation/Type')
    ->setTypeNamespace('OpenEuropa\EPoetry\TicketValidation\Type')
    ->setClientDestination('src/TicketValidation')
    ->setClientName('ClientCertificateClient')
    ->setClientNamespace('OpenEuropa\EPoetry\TicketValidation')
    ->setClassMapDestination('src/TicketValidation')
    ->setClassMapName('ClientCertificateClassmap')
    ->setClassMapNamespace('OpenEuropa\EPoetry\TicketValidation')
    ->addRule(new Rules\AssembleRule(new Assembler\GetterAssembler(new Assembler\GetterAssemblerOptions())))
    ->addRule(new Rules\AssembleRule(new Assembler\ImmutableSetterAssembler(
        new Assembler\ImmutableSetterAssemblerOptions()
    )))
    ->addRule(
        new Rules\IsRequestRule(
            $engine->getMetadata(),
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\RequestAssembler()),
                new Rules\AssembleRule(new Assembler\ConstructorAssembler(new Assembler\ConstructorAssemblerOptions())),
            ])
        )
    )
    ->addRule(
        new Rules\IsResultRule(
            $engine->getMetadata(),
            new Rules\MultiRule([
                new Rules\AssembleRule(new Assembler\ResultAssembler()),
            ])
        )
    )
;
