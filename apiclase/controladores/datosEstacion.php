<?php
//38
class datosEstacion{
    const NOMBRE_TABLA = "datos";
    const ID = "id";
    const DATE = "date";
    const TIME = "time";
    const TEMP_OUT = "temp_out";
    const HI_TEMP = "Hi_Temp";
    const LOW_TEMP = "Low_Temp";
    const OUT_HUM = "Out_Hum";
    const DEW_PT = "Dew_Pt";
    const WIND_SPEED = "Wind_Speed";
    const WIND_DIR= "Wind_Dir";
    const WIND_RUN = "Wind_run";
    const HIGH_SPEED = "High_Speed";
    const HI_DIR = "Hi_Dir";
    const WIND_CHILL = "Wind_Chill";
    const HEAT_INDEX = "Heat_Index";
    const THW_INDEX = "THW_Index";
    const THSW_INDEX = "THSW_Index";
    const BAR = "Bar";
    const RAIN = "Rain";
    const RAIN_RATE = "Rain_Rate";
    const SOLAR_RAD = "	Solar_Rad";
    const SOLAR_ENERGY = "Solar_Energy";
    const HIGH_SOLAR_RAD = "Hi_Solar_Rad";
    const UV_INDEX = "UV_Index";
    const UV_DOSE = "UV_Dose";
    const HI_UV = "Hi_UV";
    const HEAT_DD = "Heat_DD";
    const COOL_DD = "Cool_DD";
    const IN_TEMP = "In_Temp";
    const IN_HUM = "In_Hum";
    const IN_DEW = "In_Dew";
    const IN_HEAT = "In_Heat";
    const IN_EMC = "In_EMC";
    const IN_AIR_DENSITY = "In_Air_Density";
    const ET = "Et";
    const WIND_SAMP = "Wind_Samp";
    const WIND_TX = "Wind_TX";
    const ISS_RECEPT = "ISS_Recept";
    const ARC_INT = "Arc_Int";

    const CODIGO_EXITO = 1;
    const ESTADO_EXITO = 1;
    const ESTADO_ERROR = 2;
    const ESTADO_ERROR_BD = 3;
    const ESTADO_ERROR_PARAMETROS = 4;
    const ESTADO_NO_ENCONTRADO = 5;



     /**
     *  /servicios/:id  ---> devuelve los datos del servicio con id = :idParam
     *  /servicios/  ---> devuelve los datos de todos los servicios
     * 
    */

    public static function get($peticion)
    {
        //Para validar que se proporcionó una API KEY válida
        //$idUsuario = usuarios::autorizar();
        $id = $peticion ? $peticion[0]: '';
        //echo ('$peticion = ' . $peticion);

        if ($id != ''){
            if (intval($id))
                return self::listarServicio($id);
            else {
                throw new ExcepcionApi(self::ESTADO_ERROR_PARAMETROS, "Id no válido ...", 422);
            }
        }else
            return self::listarServicios();
    }

    public static function post($peticion)
    {
        //$idUsuario = usuarios::autorizar();

        /*$body = file_get_contents('php://input');
        $servicio = json_decode($body);*/
        //$idServicio = servicios::crear($servicio);
        $id = datosEstacion::cargarDatos();

        http_response_code(201);
        return [
            "estado" => self::CODIGO_EXITO,
            "mensaje" => "Servicio creado",
        ];

    }

    public static function delete( $peticion)
    {
        //$idUsuario = usuarios::autorizar();

        $id = $peticion[0];

        if (!empty($peticion[0])) {
            if (self::eliminar($id, $peticion[0]) > 0) {
                http_response_code(200);
                return [
                    "estado" => self::CODIGO_EXITO,
                    "mensaje" => "Registro eliminado correctamente",
                    //"registroEliminados" => $numRegs
                ];
            } else {
                throw new ExcepcionApi(self::ESTADO_NO_ENCONTRADO,
                    "El contacto al que intentas acceder no existe", 404);
            }
        } else {
            throw new ExcepcionApi(self::ESTADO_ERROR_PARAMETROS, "Falta id", 422);
        }
    }

     /**
     * Obtiene la colecci�n de contactos o un solo contacto indicado por el identificador
     * @param int $idUsuario identificador del usuario
     * @param null $idContacto identificador del contacto (Opcional)
     * @return array registros de la tabla contacto
     * @throws Exception
     */

     private static function listarServicio($id)
     {
         try {
             if (isset($id)) {
                 $comando = "SELECT * FROM " . self::NOMBRE_TABLA .
                     " WHERE " . self::ID . "=?";
 
                 //echo ("Valor comando = " . $comando);
 
                 // Preparar sentencia
                 $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
                 // Ligar idUsuario
                 $sentencia->bindParam(1, $id, PDO::PARAM_INT);
 
             }
 
             // Ejecutar sentencia preparada
             if ($sentencia->execute()) {
                 http_response_code(200);
                 return
                     [
                         "estado" => self::ESTADO_EXITO,
                         "datos" => $sentencia->fetchAll(PDO::FETCH_ASSOC)
                     ];
             } else
                 throw new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error");
 
         } catch (PDOException $e) {
             throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
         }
     }
 
 
     private static function listarServicios()
     {
         try {
             
             $comando = "SELECT * FROM " . self::NOMBRE_TABLA;
 
             echo ("Valor comando = " . $comando);
 
             // Preparar sentencia
             $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
 
             // Ejecutar sentencia preparada
             if ($sentencia->execute()) {
                 http_response_code(200);
                 return
                     [
                         "estado" => self::ESTADO_EXITO,
                         "datos" => $sentencia->fetchAll(PDO::FETCH_ASSOC)
                     ];
             } else
                 throw new ExcepcionApi(self::ESTADO_ERROR, "Se ha producido un error");
 
         } catch (PDOException $e) {
             throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
         }
     }

     private static function cargarDatos(){

        if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
            // Conexión a la base de datos
            //$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
            $pdo = ConexionBD::obtenerInstancia()->obtenerBD();
            
            // Preparar la consulta para inserción
            //$consulta = $conn->prepare("INSERT INTO tabla (columna1, columna2, columna3) VALUES (?, ?, ?)");
            $comando = $pdo -> prepare( "INSERT INTO " . self::NOMBRE_TABLA . " ( " .
                    //self:: ID . "," .
                    self:: DATE . "," .
                    self:: TIME . "," .
                    self:: TEMP_OUT . "," .
                    self:: HI_TEMP .  "," .
                    self:: LOW_TEMP . "," .
                    self:: OUT_HUM . "," .
                    self:: DEW_PT . "," .
                    self:: WIND_SPEED . "," .
                    self:: WIND_DIR . "," .
                    self:: WIND_RUN . "," .
                    self:: HIGH_SPEED . "," .
                    self:: HI_DIR . "," .
                    self:: WIND_CHILL . "," .
                    self:: HEAT_INDEX . "," .
                    self:: THW_INDEX .  "," .
                    self:: THSW_INDEX . "," .
                    self:: BAR . "," .
                    self:: RAIN . "," .
                    self:: RAIN_RATE . "," .
                    self:: SOLAR_RAD . "," .
                    self:: SOLAR_ENERGY . "," .
                    self:: HIGH_SOLAR_RAD . "," .
                    self:: UV_INDEX . "," .
                    self:: UV_DOSE . "," .
                    self:: HI_UV .  "," .
                    self:: HEAT_DD . "," .
                    self:: COOL_DD . "," .
                    self:: IN_TEMP . "," .
                    self:: IN_HUM . "," .
                    self:: IN_DEW . "," .
                    self:: IN_HEAT . "," .
                    self:: IN_EMC . "," .
                    self:: IN_AIR_DENSITY . "," .
                    self:: ET . "," .
                    self:: WIND_SAMP .  "," .
                    self:: WIND_TX . "," .
                    self:: ISS_RECEPT .  "," .
                    self:: ARC_INT . ")" .
                    " VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    
                //$sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($consulta);
                 

            // Leer el archivo y cargar los datos en la base de datos
            $archivo = fopen($_FILES['archivo']['tmp_name'], 'r');
            while (($linea = fgets($archivo)) !== false) {
                $datos = explode(',', trim($linea));  // Suponiendo que los datos están separados por comas
                // Realizar la inserción en la base de datos
                //$comando->execute($datos);
                //echo "comando=" . $linea;
                var_dump ($datos);
                $date = $datos[0];
                $time = $datos[1];
                $tempOut = $datos[2];
                $hiTemp = $datos[3];
                $lowTemp = $datos[4];
                $outHum = $datos[5];
                $dewPt = $datos[6];
                $windSpeed = $datos[7];
                $windDir = $datos[8];
                $windRun = $datos[9];
                $highSpeed = $datos[10];
                $hiDir = $datos[11];
                $windChill = $datos[12];
                $heatIndex = $datos[13];
                $thwIndex = $datos[14];
                $thswIndex = $datos[15];
                $bar = $datos[16];
                $rain = $datos[17];
                $rainRate = $datos[18];
                $solarRad = $datos[19];
                $solarEnergy = $datos[20];
                $highSolarRad = $datos[21];
                $uvIndex = $datos[22];
                $uvDose = $datos[23];
                $hiUv = $datos[24];
                $heatDD = $datos[25];
                $coolDD = $datos[26];
                $inTemp = $datos[27];
                $inHum = $datos[28];
                $inDew = $datos[29];
                $inHeat = $datos[30];
                $inEcm = $datos[31];
                $inAirDensity = $datos[32];
                $et = $datos[33];
                $windSamp = $datos[34];
                $windTx = $datos[35];
                $issRecept = $datos[36];
                $arcInt = $datos[37];

                // Preparar la sentencia
                //$sentencia = $pdo->prepare($comando);

                $comando->bindParam(1, $date);
                $comando->bindParam(2, $time);
                $comando->bindParam(3, $tempOut);
                $comando->bindParam(4, $hiTemp);
                $comando->bindParam(5, $lowTemp);
                $comando->bindParam(6, $outHum);
                $comando->bindParam(7, $dewPt);
                $comando->bindParam(8, $windSpeed);
                $comando->bindParam(9, $windDir);
                $comando->bindParam(10, $windRun);
                $comando->bindParam(11, $highSpeed);
                $comando->bindParam(12, $windDir);
                $comando->bindParam(13, $windRun);
                $comando->bindParam(14, $highSpeed);
                $comando->bindParam(15, $hiDir);
                $comando->bindParam(16, $windChill);
                $comando->bindParam(17, $heatIndex);
                $comando->bindParam(18, $thwIndex);
                $comando->bindParam(19, $thswIndex);
                $comando->bindParam(20, $bar);
                $comando->bindParam(21, $rain);
                $comando->bindParam(22, $rainRate);
                $comando->bindParam(23, $solarRad);
                $comando->bindParam(24, $solarEnergy);
                $comando->bindParam(25, $highSolarRad);
                $comando->bindParam(26, $uvIndex);
                $comando->bindParam(27, $uvDose);
                $comando->bindParam(28, $hiUv);
                $comando->bindParam(29, $heatDD);
                $comando->bindParam(30, $coolDD);
                $comando->bindParam(31, $inTemp);
                $comando->bindParam(32, $inHum);
                $comando->bindParam(33, $inDew);
                $comando->bindParam(34, $inHeat);
                $comando->bindParam(35, $inEcm);
                $comando->bindParam(36, $inAirDensity);
                $comando->bindParam(37, $issRecept);
                $comando->bindParam(38, $arcInt);

                $comando->execute();
                
                
                //$sentencia->execute();
               

            }
    
            // Cerrar el archivo y la conexión a la base de datos
            fclose($archivo);
            $pdo = null;
    
            echo 'Datos cargados correctamente.';
        } else {
            echo 'No se ha proporcionado ningún archivo.';
        }
     }

     private static function eliminar($id)
     {
         try {
             // Sentencia DELETE
             $comando = "DELETE FROM " . self::NOMBRE_TABLA .
                 " WHERE " . self::ID . "=?";
 
             // Preparar la sentencia
             $sentencia = ConexionBD::obtenerInstancia()->obtenerBD()->prepare($comando);
 
             $sentencia->bindParam(1, $id);
            // $sentencia->bindParam(2, $idUsuario);
 
             $sentencia->execute();
 
             return $sentencia->rowCount();
 
         } catch (PDOException $e) {
             throw new ExcepcionApi(self::ESTADO_ERROR_BD, $e->getMessage());
         }
     }
}
?>