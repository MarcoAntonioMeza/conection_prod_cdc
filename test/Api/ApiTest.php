<?php
namespace RCCFicoScore\Client;

use \GuzzleHttp\Client;
use \GuzzleHttp\HandlerStack as handlerStack;

use \RCCFicoScore\Client\Api\RCCFicoScoreApi;
use \RCCFicoScore\Client\ApiException;
use \RCCFicoScore\Client\Configuration;
use RCCFicoScore\Client\Model\CatalogoEstados;
use RCCFicoScore\Client\Model\PersonaPeticion;
use RCCFicoScore\Client\Model\DomicilioPeticion;

use Signer\Manager\Interceptor\MiddlewareEvents;
use Signer\Manager\Interceptor\KeyHandler;

class RCCFicoScoreApiTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $password ='ls:crm*123';//  getenv('KEY_PASSWORD');
        //print_r($password);die;
        $this->signer = new KeyHandler('././lib\Interceptor\keypair.p12', '././lib\Interceptor\cdc_cert.pem', $password);
        //$this->signer = new KeyHandler(null, null, $password);

        $events = new MiddlewareEvents($this->signer);
        $handler = handlerStack::create();
        $handler->push($events->add_signature_header('x-signature'));
        $handler->push($events->verify_signature_header('x-signature'));
        $client = new Client(['handler' => $handler]);

        $config = new Configuration();
        $config->setHost('https://services.circulodecredito.com.mx/v2/rccficoscore/sandbox');
        
        $this->apiInstance = new RCCFicoScoreApi($client, $config);
        $this->x_api_key = "Hxg6ihfb0SGtFuBHxh7MchEG3eyvUKAV";
        $this->username = "JMD1232ACR";
        $this->password = "Mapco082024$";
         
    }

    public function testGetReporte()
    {
        $estado = new CatalogoEstados();
        $request = new PersonaPeticion();
        $domicilio = new DomicilioPeticion();

        $request->setApellidoPaterno("MEZA");
        $request->setApellidoMaterno("SAMPEDRO");
        $request->setApellidoAdicional(null);
        $request->setPrimerNombre("MARCO ANTONIO");;
        $request->setSegundoNombre(null);
        $request->setFechaNacimiento("2001-08-28");
        $request->setRfc("MESM010828FR7");
        $request->setCurp(null);
        $request->setNacionalidad('MX');
        $request->setResidencia(null);
        $request->setEstadoCivil(null);
        $request->setSexo(null);
        $request->setClaveElectorIfe(null);
        $request->setNumeroDependientes(null);
        $request->setFechaDefuncion(null);

        $domicilio->setDireccion(" FRANCISCO I MADERO 06");
        $domicilio->setColoniaPoblacion("VILLA ALTA");
        $domicilio->setDelegacionMunicipio("TEPETITLA DE LARDIZABAL");
        $domicilio->setCiudad( "TEPETITLA DE LARDIZABAL");
        $domicilio->setEstado($estado::TLAX);
        $domicilio->setCp("90700");
        $domicilio->setFechaResidencia(null);
        $domicilio->setNumeroTelefono(null);
        $domicilio->setTipoDomicilio(null);
        $domicilio->setTipoAsentamiento(null);
        $request->setDomicilio($domicilio);

        try {
            $result = $this->apiInstance->getReporte($this->x_api_key, $this->username, $this->password, $request);
            $this->signer->close();
            print_r($result);
            $this->assertTrue($result->getFolioConsulta()!==null);
            return $result->getFolioConsulta();
        } catch (ApiException $e) {
            echo 'Exception when calling RCCFicoScoreApi->getReporte: ', $e->getMessage(), PHP_EOL;
        }
    }

}
