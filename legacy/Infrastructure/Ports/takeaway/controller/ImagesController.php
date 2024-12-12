<?php



require_once("model/ImagesModel.php");

/**
 * Controlador para la gestión de imágenes
 *
 * @author manager
 */
class ImagesController extends \TakeawayController{

    /**
    * @ignore
    * Constructor
    */
    public function __construct(){
       parent::__construct(true);
    }

    /**
     * Procedimiento para cargar la colección de imágenes de un producto
     * @param int id Identidad del producto
     * @return string
     */
    public function Index($id = 0){
        try{
            // Instanciar el modelo
            $model = new \ImagesModel();
            // Cargar categorías
            $model->GetImages($id);
            // Retornar la vista renderizada
            return $this->PartialView($model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Index", $e);
        }
    }

    /**
     * Procedimiento para almacenar una categoría
     * @param int $id Identidad del producto
     * @return string vista renderizada
     */
    public function Save($id = 0){
        try{
            $reference = filter_input(INPUT_POST, "Reference");
            // Obtener array de rutas
            $paths = $this->UploadImages($reference);
            // Instanciar el modelo
            $model = new \ImagesModel();
            // Cargar categorías
            $model->SaveImages($id, $paths);
            // Cargar la lista de imágenes del producto
            $model->GetImages($id);
            // Retornar la vista renderizada
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Save", $e);
        }
    }

    /**
     * Procedimiento de eliminación de una categoría
     * @param int $prod Identidad del producto
     * @param int $id Identidad de la imagen
     * @return string Vista renderizada
     */
    public function Delete($prod=0, $id = 0){
        try{
            $entity = $this->GetEntity("Image");
            // Instanciar el modelo
            $model = new \ImagesModel();
            // Cargar categorías
            $model->Delete($entity->Id);
            // Cargar la lista de imágenes del producto
            $model->GetImages($prod);
            // Retornar la vista renderizada
            return $this->Partial("Index", $model);
        }
        catch(Exception $e){
            // Procesado del error
            return $this->ProcessError("Delete", $e);
        }
    }

    /**
     * Proceso de subida de imagen
     * @param string $fileName Nombre del fichero
     * @return string
     */
    private function UploadFile($fileName = ""){

        $reference = (isset($_REQUEST["Reference"])
                && !empty($_REQUEST["Reference"]))
                ? strip_tags($_REQUEST["Reference"]): "";

        if(!empty($reference) && !empty($fileName)
                && !empty($_FILES["file"]["name"])){
            // Obtener la ruta base
            $basePath = ConfigurationManager::GetKey( "img-path" );
            // Asignar el directorio de producto
            $path = str_replace( "{Reference}", $reference ,$basePath);
            // Validamos si el directorio existe
            if(is_dir($path)==false){
               mkdir($path, 0777);
            }
            Uploader::UploadFile("file", $path,["jpg"]);
            return $path.$_FILES["file"]["name"];
        }
        return "";
    }

    /**
     * Proceso para almacenar todas las imágenes
     */
    private function UploadImages($reference = ""){

        $paths = [];
        if(!isset($_FILES["files"]) || empty($reference)){
            return $paths;
        }

        $date = new \DateTime("NOW");
        // Obtener la ruta base
        $basePath = ConfigurationManager::GetKey( "img-path" );
        // Asignar el directorio de producto
        $path = str_replace( "{Reference}", $reference ,$basePath );
        // Validamos si el directorio existe
        if(is_dir($path) == false){ mkdir($path, 0777); }

        foreach ($_FILES["files"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                // nombre del fichero temporal
                $tmp_folder = $_FILES["files"]["tmp_name"][$key];
                $tmp_name = $_FILES["files"]["name"][$key];
                $tmp = "tmp/$tmp_name";
                move_uploaded_file($tmp_folder, $tmp);
                // Obtener mimetype
                $mime = $this->getMimeType($tmp);
                if($mime <= 0){ continue; }
                // Obtener extensión
                $ext = $this->getExtension($mime);
                if(empty($ext)){ continue; }
                // Obtener el recurso
                $image = $this->getImageResource($mime, $tmp);
                if($image == null){continue;}
                // Obtener nombre de la imagen
                $name = sha1_file($tmp).md5_file($tmp).".$ext";
                // directorio destino
                $dst_path = str_replace("//", "/", "$path/$name");
                // exportar imagen
                if($this->exportImage($mime, $image, $dst_path)){
                    $paths[$name] = "$path/$name";
                }
                unlink($tmp);
            }
            else {
                $this->Log->LogError("Error al subir fichero código: ". $error);
            }
        }
        return $paths;
    }

    private function getExtension($mime = 0){
        $ext = "";
        // carga la imagen
        switch ($mime){

            case IMAGETYPE_GIF:
                $ext = "gif";
                break;

            case IMAGETYPE_JPEG:
                $ext = "jpg";
                break;

            case IMAGETYPE_PNG:
                $ext = "png";
                break;
        }
        return $ext;
    }

    private function getMimeType($path = ""){
        // datos de la imagen
        list($width, $height, $mime) = getimagesize($path);
        // validar MimeType
        if($mime == IMAGETYPE_GIF || $mime == IMAGETYPE_JPEG
                || $mime == IMAGETYPE_PNG){
            return $mime;
        }
        return -1;
    }

    private function getImageResource($mime = 0, $path = ""){
        $image = null;
        // carga la imagen
        switch ($mime){

            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($path);
                break;

            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($path);
                break;

            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($path);
                break;
        }
        return $image;
    }

    private function exportImage($mime = 0, $image = null, $dst_path = ""){

        $export = false;

        switch ($mime){

            case IMAGETYPE_GIF:
                $export = imagegif($image, $dst_path);
                break;

            case IMAGETYPE_JPEG:
                $export = imagejpeg($image, $dst_path);
                break;

            case IMAGETYPE_PNG:
                $export = imagepng($image, $dst_path);
                break;
        }

        if($image != null){
            imagedestroy($image);
        }

        return $export;
    }

}
