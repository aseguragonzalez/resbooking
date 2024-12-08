<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <title>Resbooking</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"
              rel="stylesheet" type="text/css" />
        <script src="http://code.jquery.com/jquery-1.11.3.min.js"
                type="text/javascript" ></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"
                type="text/javascript" ></script>
    </head>
    <body>

        <div class="container">

            <div class="page-header">
                <h1>Resbooking - Generación de la librería</h1>
            </div>

            <p>
                Lorem ipsum ad his scripta blandit partiendo, eum fastidii
                accumsan euripidis in, eum liber hendrerit an. Qui ut wisi
                vocibus suscipiantur, quo dicit ridens inciderint id. Quo mundi
                lobortis reformidans eu, legimus senserit definiebas an eos.
                Eu sit tincidunt incorrupte definitionem, vis mutat affert
                percipit cu, eirmod consectetuer signiferumque eu per. In usu
                latine equidem dolores. Quo no falli viris intellegam, ut fugit
                veritus placerat per.
            </p>

            <form action="download.php" method="POST" class="col-lg-4 col-md-4" >

                <h2>Formulario de descarga</h2>

                <div class="form-group">
                    <label>Nombre del paquete</label>
                    <input type="text" class="form-control" name="name"
                           maxlength="30" required="required" value="resbooking.lib" />
                </div>

                <div class="form-group form-inline">

                    <label>Opciones de descarga</label>

                    <br />

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="debug" />Debug
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="min" />Min
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" role="button">
                        Descargar
                    </button>
                    <button type="reset" class="btn btn-default" role="button">
                        Borrar
                    </button>
                </div>

            </form>

        </div>
    </body>
</html>
