<?php

    $test = [
        ["Id" => "chk_test_booking_management",
            "Name" => "Booking Management",
            "Desc" => "Test del Management..."]
    ];

?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <title>Resbooking</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <script src="js/jquery.min.js" type="text/javascript" ></script>
        <script src="js/bootstrap.min.js" type="text/javascript" ></script>
        <style>
            #download, #test{
                padding:20px;
            }
        </style>
    </head>
    <body>

        <div class="container">

            <div class="page-header">
                <h1>Resbooking - Test</h1>
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

            <form action="test.php" method="POST" class="col-md-4 col-lg-4"
                  enctype="multipart/form-data">

                <h2>Formulario de test</h2>

                <div class="form-group">
                    <label>Paquete de clases</label>
                    <input type="file" class="form-control"
                           name="lib" required="required" />
                </div>

                <div class="form-group">
                    <label>Fichero de configuración</label>
                    <input type="file" class="form-control"
                           name="test" required="required" />
                </div>

                <div class="form-group ">
                    <label>Test Disponibles</label>
                    <?php foreach($test as $item): ?>
                    <div class="checkbox">
                        <label title="<?php echo $item["Desc"] ?>" >
                            <input type="checkbox"
                                   name="<?php echo $item["Id"] ?>" />
                            <?php echo $item["Name"] ?>
                        </label>
                    </div>
                    <?php endforeach;?>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary" role="button">
                        Ejecutar
                    </button>
                    <button type="reset" class="btn btn-default" role="button">
                        Borrar
                    </button>
                </div>

            </form>

        </div>
    </body>
</html>
