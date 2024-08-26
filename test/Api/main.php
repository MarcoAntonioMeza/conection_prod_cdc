
<?php
require '../../vendor/autoload.php';

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

use \RCCFicoScore\Client\ObjectSerializer;



class RCCFicoScoreApiTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $basePath = __DIR__;

        // Construye la ruta completa a los archivos
        $keypairPath = $basePath . '/../../lib/Interceptor/keypair.p12';
        $certPath = $basePath . '/../../lib/Interceptor/cdc_cert.pem';
        $password = 'ls:crm*123'; //  getenv('KEY_PASSWORD');
        //print_r($password);die;
        $this->signer = new KeyHandler($keypairPath, $certPath, $password);
        //$this->signer = new KeyHandler(null, null, $password);
        //lib\Interceptor\keypair.p12

        $events = new MiddlewareEvents($this->signer);
        $handler = handlerStack::create();
        $handler->push($events->add_signature_header('x-signature'));
        $handler->push($events->verify_signature_header('x-signature'));
        $client = new Client(['handler' => $handler]);

        $config = new Configuration();
        $config->setHost('https://services.circulodecredito.com.mx/v2/rccficoscore/');

        $this->apiInstance = new RCCFicoScoreApi($client, $config);
        $this->x_api_key = "Hxg6ihfb0SGtFuBHxh7MchEG3eyvUKAV";
        $this->username = "JMD1232ACR";
        $this->password = "Mapco082024$";
    }

    public function testGetReporte($data)
    {
        $data = json_decode($data, true);
        
        $estado = new CatalogoEstados();
        $request = new PersonaPeticion();
        $domicilio = new DomicilioPeticion();

        $request->setApellidoPaterno($data['apellidoPaterno']);
        $request->setApellidoMaterno($data['apellidoMaterno']);
        $request->setApellidoAdicional(null);
        $request->setPrimerNombre($data['primerNombre']);
        $request->setSegundoNombre(null);
        $request->setFechaNacimiento($data['fechaNacimiento']);
        $request->setRfc($data['RFC']);
        $request->setCurp(null);
        $request->setNacionalidad('MX');
        $request->setResidencia(null);
        $request->setEstadoCivil(null);
        $request->setSexo(null);
        $request->setClaveElectorIfe(null);
        $request->setNumeroDependientes(null);
        $request->setFechaDefuncion(null);

        $domicilio->setDireccion($data['domicilio']['direccion']);
        $domicilio->setColoniaPoblacion($data['domicilio']['coloniaPoblacion']);
        $domicilio->setDelegacionMunicipio($data['domicilio']['delegacionMunicipio']);
        $domicilio->setCiudad($data['domicilio']['ciudad']);;
        $domicilio->setEstado($data['domicilio']['estado']);
        $domicilio->setCp($data['domicilio']['CP']);
        $domicilio->setFechaResidencia(null);
        $domicilio->setNumeroTelefono(null);
        $domicilio->setTipoDomicilio(null);
        $domicilio->setTipoAsentamiento(null);
        $request->setDomicilio($domicilio);

        try {
            $result =  $this->apiInstance->getReporte($this->x_api_key, $this->username, $this->password, $request);
            $this->signer->close();
            //print_r($result);
            $this->assertTrue($result->getFolioConsulta() !== null);
                //Utiliza ObjectSerializer para convertir el objeto a un formato serializable
            $sanitizedResult = ObjectSerializer::sanitizeForSerialization($result);

            $response = [
                'code' => 202,
                'data' => $sanitizedResult,
            ];
            return json_encode($response);
            //return $result->getFolioConsulta();
        } catch (ApiException $e) {
            $response = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()  // El mensaje es ya un string
            ];
            return json_encode($response);
            //echo 'Exception when calling RCCFicoScoreApi->getReporte: ', $e->getMessage(), PHP_EOL;
        }
    }
}



header("Content-Type: application/json");  // Establece el tipo de contenido a JSON

// Solo permitir solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);  // Método no permitido
    echo json_encode(['code' => 10, 'error' => 'Método no permitido. Solo se permite POST.']);
    exit;
}

// Leer y decodificar los datos de la solicitud POST
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);  // Solicitud incorrecta
    echo json_encode(['code' => 10,   'error' => 'Datos de entrada no válidos']);
    exit;
}



$api = new RCCFicoScoreApiTest();
$api->setUp();
//echo json_encode(['code' => 10,   'error' => 'Datos de entrada no válidos', 'data' => $input]);
echo $api->testGetReporte($input);