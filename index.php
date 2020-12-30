<!DOCTYPE html>

<?php
    session_start();
    require_once('./login/functions.php');
    require_once("./db.php");

    $queryUsers = "SELECT Usuario, Contraseña FROM Usuarios";
    
    $rsDATAUsers = $DBengine->query($queryUsers);

    if ($rsDATAUsers->num_rows>0) {

        $dataUsers = [];
        $filas = [];
        while ($registerUsers = $rsDATAUsers -> fetch_row()) {
            $fila = [];
            $fila = ["User" => $registerUsers[0],"Pass" => $registerUsers[1]];
            array_push($filas, $fila);
        }
        array_push($dataUsers,["Sesion" => ["User" => session_get('User'),"Pass" => session_get('Pass')],"Db" => $filas]);

        $isLoged = 0;

        for ($i=0; $i<count($dataUsers[0]["Db"]); $i++) {
            if ($dataUsers[0]["Db"][$i]["User"] == $dataUsers[0]["Sesion"]["User"] && $dataUsers[0]["Db"][$i]["Pass"] == $dataUsers[0]["Sesion"]["Pass"]) {

                //El código va acá

                

                //Fin código

                $isLoged = 1;
                //return;
            }
        }

        if ($isLoged == 0) {
            header('Location: /CentroMedicoDelCarmen/login');
        }

        

    } else {
        header('Location: /CentroMedicoDelCarmen/login');
        //echo "No hay usuarios registrados";
    }
    

?>

<html lang="es">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Exportar a PDF -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.2/jspdf.min.js"></script>
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.2.0/jspdf.umd.min.js"></script> -->
        <script src="https://unpkg.com/jspdf-autotable@3.5.13/dist/jspdf.plugin.autotable.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <!-- Fin Exportar a PDF -->

        <?php include './assets/img/favicon/favicon.php' ?>

        <title>Centro Médico Del Carmen</title>


        


    </head>

    <script>

        var idAnterior = -1;

        var ObraSocial_Nombre_Anterior = null;
        var Periodo_Mes_Anterior = null;
        var Periodo_Ano_Anterior = null;
      
      function addPlanilla(){
        //alert("To Do!");
        //pantalla1();
        listarTiposPlanillas();
        listarObrasSociales();
        listarMeses();
        listarAños();
        $("#addPlanillaModal").modal();
      }


      function listarTiposPlanillas(){
        $.ajax({
                url: './listarTiposPlanillas.php' ,
                type: 'POST' ,
                dataType: 'html',
            })
            .done(function(respuesta){
                respuesta=JSON.parse(respuesta);
                //console.log(respuesta)
                $("#Planillas").empty();
                console.log(respuesta);

                $("#Planillas").html(respuesta);
                var x = document.getElementById("Planillas");
                c = document.createElement("option");
                c.text = "Seleccione un tipo de planilla";
                x.options.add(c);
                c.selected = true;
                c.hidden = true;
                c.value = 0;
                x.options.add(c);
               // console.log(c);
                for (var i = 0; i < respuesta.length; i++) {
                    c = document.createElement("option");
                    c.text = respuesta[i].Nombre;
                    c.value = respuesta[i].ID;
                    x.options.add(c);
                    //console.log(c);
                }
            })
            .fail(function(){
                console.log("error");
            });
      }


      function listarObrasSociales(){
        $.ajax({
                url: './listarObrasSociales.php' ,
                type: 'POST' ,
                dataType: 'html',
            })
            .done(function(respuesta){
                respuesta=JSON.parse(respuesta);
                $("#ObrasSociales").empty();
                console.log(respuesta);

                //$("#ObrasSociales").html(respuesta);
                var x = document.getElementById("ObrasSociales");
                c = document.createElement("option");
                c.text = "Seleccione una obra social";
                x.options.add(c);
                c.selected = true;
                c.hidden = true;
                c.value = 0;
                x.options.add(c);
               // console.log(c);
                for (var i = 0; i < respuesta.length; i++) {
                    c = document.createElement("option");
                    c.text = respuesta[i].Nombre;
                    c.value = respuesta[i].ID;
                    x.options.add(c);
                    //console.log(c);
                }
            })
            .fail(function(){
                console.log("error");
            });
      }


      function listarMeses(){

        var MesesArray = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]
        $("#Meses").empty();
        x = document.getElementById("Meses");
        for (var i = 0; i < 12; i++) {
            c = document.createElement("option");
            c.text = MesesArray[i];
            c.value = 1+i;
            x.options.add(c);
            console.log(c);
        }

        var MesActual = Number(moment().format("MM"));
        $("#Meses").val(MesActual);

      }


      function listarAños(){

          $("#Años").empty();

          var x = document.getElementById("Años");
          var c;
          var AnoActual = Number(moment().format("YYYY"))+1;
          for (var i = 0; i < 5; i++) {
              c = document.createElement("option");
              c.text = --AnoActual;
              c.value = AnoActual;
              x.options.add(c);
              console.log(c)
          }
      }


      function guardarPlanilla(){

        if ($("#Planillas").val()==0) {
            //console.log("Hi");
            $('#Planillas').tooltip('show');
            return;
        }

        if ($("#ObrasSociales").val()==0) {
            //console.log("Hi");
            $('#ObrasSociales').tooltip('show');
            return;
        }

       /* var mesPeriodo = "";

        if (Number($("#Meses").val()) <10) {
            mesPeriodo = "0" + $("#Meses").val().toString();
        }else{
            mesPeriodo = $("#Meses").val().toString();
        }*/

        var obj = { 
            ObraSocial_ID: Number($("#ObrasSociales").val()), 
            TipoPlanilla_ID: Number($("#Planillas").val()), 
            Periodo_Mes: Number($("#Meses").val()) ,
            Periodo_Ano: Number($("#Años").val())
        };

        console.log(obj);
        var jsonString = JSON.stringify(obj);
        console.log(jsonString) ;


         $.ajax({
            url: './addPlanilla.php' ,
            type: 'POST' ,
            dataType: 'html',
            data: {consulta: jsonString},
        })
        .done(function(respuesta){
            //console.log("Hola");
            respuesta=JSON.parse(respuesta);    
            console.log(respuesta[0].status);
            if(respuesta[0].error == 0){
                $("#addPlanillaModal").modal('hide');
                $("#search").val("");
                buscar_datos();
            }else{
                $("#addPlanillaModal").modal('hide');
                $("#textErrorAddPlantilla").html(respuesta[0].status);
                $("#errorInAddPlantillaModal").modal();
            }

        })
        .fail(function(){
            console.log("Error")
        });



      }



      function addFila(ID, ObraSocial_Nombre, TipoPlanilla_Nombre, Mes, Año, TipoPlanilla_ID){
        //alert(id);
        $("#IDPlanilla").val(ID);
        $("#TipoPlanilla").val(TipoPlanilla_ID);
        $("#Profesionales").empty();
        $("#datosProfesional").html("");
        $("#MeProfesional").html("");
        $("#addFilaEspecialidades").empty();
        $("#addFilaPracticas").empty();
        $("#CantidadInput").val("");
        $("#ValorUnitarioInput").val("");
        $("#TotalInput").html("");
        $("#addFilaModalLabel").html(TipoPlanilla_Nombre + ": " + ObraSocial_Nombre + " - " + Mes + "/" + Año);
        listarProfesionales();
        $('#datosProfesional').css('display', 'none');
        $('#DivEspecialidad').css('display', 'none');
        $('#DivPractica').css('display', 'none');
        $('#DivCantidad').css('display', 'none');
        $('#TotalInput').css('display', 'none');
        

        $("#addFilaModal").modal();
      }




      function listarProfesionales(){
        $.ajax({
                url: './listarProfesionales.php' ,
                type: 'POST' ,
                dataType: 'html',
            })
            .done(function(respuesta){
                respuesta=JSON.parse(respuesta);
                $("#Profesionales").empty();
                console.log(respuesta);

                $("#Profesionales").html(respuesta);
                var x = document.getElementById("Profesionales");
                c = document.createElement("option");
                c.text = "Seleccione una Profesional";
                //x.options.add(c);
                c.selected = true;
                c.hidden = true;
                c.value = 0;
                x.options.add(c);
               // console.log(c);
                for (var i = 0; i < respuesta.length; i++) {
                    c = document.createElement("option");
                    c.text = respuesta[i].Profesionales_Nombre + " " + respuesta[i].Profesionales_Apellido;
                    c.value = respuesta[i].Profesionales_ID;
                    x.options.add(c);
                    //console.log(c);
                }



            })
            .fail(function(){
                $('#textErrorAddFila').html("Error al tratar de listar los profesionales");
                $("#addFilaModal").modal('hide');
                $("#errorInAddFilaModal").modal();
            });
      }






      function listarPracticasFila(){


        if($("#TipoPlanilla").val() == 1){
            $.ajax({
                url: './listarConsultas.php' ,
                type: 'POST' ,
                dataType: 'html',
            })
            .done(function(respuesta){
                respuesta=JSON.parse(respuesta);
                //console.log(respuesta);
                var x = document.getElementById("addFilaPracticas");
                c = document.createElement("option");

                if(respuesta[0].error == 2){
                    $("#addFilaPracticas").empty();
                    c.text = "Esta especialidad no tiene ninguna práctica asignada";
                    c.selected = true;
                    c.hidden = true;
                    c.value = 0;
                    x.options.add(c);
                    $('#addFilaPracticas').prop('disabled', true);
                    $('#DivPractica').css('display', 'block');
                    return;
                }

               if(respuesta[0].error == 0){
                    var plantilla = "";

                    $("#addFilaPracticas").empty();

                     c.text = "Seleccione una consulta";
                    //x.options.add(c);
                    c.selected = true;
                    c.hidden = true;
                    c.value = 0;
                    x.options.add(c);
                    

                    for (var i = 0; i < respuesta[0].status.length; i++) {
                        c = document.createElement("option");
                        c.text = respuesta[0].status[i].Consultas_Nombre + " (" + respuesta[0].status[i].Consultas_Codigo + ")";
                        c.value = respuesta[0].status[i].Consultas_Codigo;
                        x.options.add(c);
                    }
                    $('#addFilaPracticas').prop('disabled', false);

                    $('#DivPractica').css('display', 'block');
                    $('#MeProfesional').css('display', 'block');
                    $('#DivCantidad').css('display', 'block');
                
               }else{

                    $('#textErrorAddFila').html(respuesta[0].status);
                    $("#addFilaModal").modal('hide');
                    $("#errorInAddFilaModal").modal();
                
               }
                   

            })
            .fail(function(){
                $('#textErrorAddFila').html("Error al tratar de listar las practicas");
                $("#addFilaModal").modal('hide');
                $("#errorInAddFilaModal").modal();
            });
            return;
        }


        var obj = { 
            Especialidad_ID: $("#addFilaEspecialidades").val()
        };

        //console.log(obj);
        var jsonString = JSON.stringify(obj);
        //console.log(jsonString) ;

         $.ajax({
            url: './listarPracticasByEspecialidad.php' ,
            type: 'POST' ,
            dataType: 'html',
            data: {consulta: jsonString},
        })
        .done(function(respuesta){
            respuesta=JSON.parse(respuesta);
            //console.log(respuesta);
            var x = document.getElementById("addFilaPracticas");
            c = document.createElement("option");

            if(respuesta[0].error == 2){
                $("#addFilaPracticas").empty();
                c.text = "Esta especialidad no tiene ninguna práctica asignada";
                c.selected = true;
                c.hidden = true;
                c.value = 0;
                x.options.add(c);
                $('#addFilaPracticas').prop('disabled', true);
                $('#DivPractica').css('display', 'block');
                return;
            }

           if(respuesta[0].error == 0){
                var plantilla = "";

                $("#addFilaPracticas").empty();

                 c.text = "Seleccione una Practica";
                //x.options.add(c);
                c.selected = true;
                c.hidden = true;
                c.value = 0;
                x.options.add(c);
                

                for (var i = 0; i < respuesta[0].status.length; i++) {
                    c = document.createElement("option");
                    c.text = respuesta[0].status[i].Practicas_Nombre + " (" + respuesta[0].status[i].Practicas_Codigo + ")";
                    c.value = respuesta[0].status[i].Practicas_ID;
                    x.options.add(c);
                }
                $('#addFilaPracticas').prop('disabled', false);

                $('#DivPractica').css('display', 'block');
                $('#DivCantidad').css('display', 'block');
            
           }else{

                $('#textErrorAddFila').html(respuesta[0].status);
                $("#addFilaModal").modal('hide');
                $("#errorInAddFilaModal").modal();
            
           }
               

        })
        .fail(function(){
            $('#textErrorAddFila').html("Error al tratar de listar las practicas");
            $("#addFilaModal").modal('hide');
            $("#errorInAddFilaModal").modal();
        });
      }



    function calcularTotal(){
        if($('#CantidadInput').val() == ""){
            $('#TotalInput').html("");
            $('#TotalInput').css('display', 'none');
            return;
        }

        if($('#ValorUnitarioInput').val() == ""){
            $('#TotalInput').html("");
            $('#TotalInput').css('display', 'none');
            return;
        }

        var Total = Number($('#CantidadInput').val()) * Number($('#ValorUnitarioInput').val());
        
        $('#TotalValor').val(Total)
        $('#TotalInput').html("Valor total = " + Total);
        $('#TotalInput').css('display', 'block');
    }



    function guardarFila(){

        if ($("#Profesionales").val()==0) {
            //console.log("Hi");
            $('#Profesionales').tooltip('show');
            return;
        }

        if ($("#addFilaEspecialidades").val()==0) {
            //console.log("Hi");
            $('#addFilaEspecialidades').tooltip('show');
            return;
        }

        if ($("#addFilaPracticas").val()==0) {
            //console.log("Hi");
            $('#addFilaPracticas').tooltip('show');
            return;
        }

        if ($("#CantidadInput").val() == "") {
            //console.log("Hi");
            $('#CantidadInput').tooltip('show');
            return;
        }

        if ($("#ValorUnitarioInput").val() == "") {
            //console.log("Hi");
            $('#ValorUnitarioInput').tooltip('show');
            return;
        }



       // alert($('#TotalValor').val());



       if($("#TipoPlanilla").val() == 1){
            var obj = { 
                Planilla_ID: Number($("#IDPlanilla").val()), 
                Especialidad_ID: Number($("#addFilaEspecialidades").val()), 
                Profesional_ID: Number($("#Profesionales").val()),
                Consulta_ID: Number($("#addFilaPracticas").val()),
                Cantidad: Number($("#CantidadInput").val()),
                Valor_Unitario: Number($("#ValorUnitarioInput").val())
            };

            //console.log(obj);
            var jsonString = JSON.stringify(obj);
            //console.log(jsonString) ;


             $.ajax({
                url: './addFilaConsulta.php' ,
                type: 'POST' ,
                dataType: 'html',
                data: {consulta: jsonString},
            })
            .done(function(respuesta){
                respuesta=JSON.parse(respuesta);    
                console.log(respuesta);
                if(respuesta[0].error == 0){
                    $("#addFilaModal").modal('hide');
                    $("#search").val("");
                    buscar_datos();
                }else{
                    $("#addFilaModal").modal('hide');
                    $("#textErrorAddFila").html(respuesta[0].status);
                    $("#errorInAddFilaModal").modal();
                }

            })
            .fail(function(){
                $('#textErrorAddFila').html("Error al tratar de agregar la fila");
                $("#addFilaModal").modal('hide');
                $("#errorInAddFilaModal").modal();
            });
            return;
       }



        var obj = { 
            Planilla_ID: Number($("#IDPlanilla").val()), 
            Especialidad_ID: Number($("#addFilaEspecialidades").val()), 
            Profesional_ID: Number($("#Profesionales").val()),
            Practica_ID: Number($("#addFilaPracticas").val()),
            Cantidad: Number($("#CantidadInput").val()),
            Valor_Unitario: Number($("#ValorUnitarioInput").val())
        };

        //console.log(obj);
        var jsonString = JSON.stringify(obj);
        //console.log(jsonString) ;


         $.ajax({
            url: './addFila.php' ,
            type: 'POST' ,
            dataType: 'html',
            data: {consulta: jsonString},
        })
        .done(function(respuesta){
            respuesta=JSON.parse(respuesta);    
            console.log(respuesta);
            if(respuesta[0].error == 0){
                $("#addFilaModal").modal('hide');
                $("#search").val("");
                buscar_datos();
            }else{
                $("#addFilaModal").modal('hide');
                $("#textErrorAddFila").html(respuesta[0].status);
                $("#errorInAddFilaModal").modal();
            }

        })
        .fail(function(){
            $('#textErrorAddFila').html("Error al tratar de agregar la fila");
            $("#addFilaModal").modal('hide');
            $("#errorInAddFilaModal").modal();
        });



      }



      function editarFila(Fila_ID, Planilla_ID, Especialidad_ID, Profesional_ID, Practica_ID, Cantidad, Valor_Unitario, TipoPlanilla_ID){
       $("#IDPlanillaEdit").val(Planilla_ID);
       $("#IDFilaEdit").val(Fila_ID);
       $("#IDTipoPlanillaEdit").val(TipoPlanilla_ID);
       $("#ProfesionalesEdit").empty();
        $("#datosProfesionalEdit").html("");
        $("#MeProfesionalEdit").html("");
        $("#editFilaEspecialidades").empty();
        $("#editFilaPracticas").empty();
        $("#CantidadInputEdit").val(Cantidad);
        $("#ValorUnitarioInputEdit").val(Valor_Unitario);
        $("#TotalInputEdit").html("Valor total = " + (Number(Cantidad) * Number(Valor_Unitario)));
        listarProfesionalesEdit(Profesional_ID,Especialidad_ID,Practica_ID);
        



        $("#editFilaModal").modal();    
      }






      function listarProfesionalesEdit(SelectedProfessional,SelectedEspecialidad, SelectedPractica){

         var obj = { 
            ID: SelectedProfessional
        };

        //console.log(obj);
        var jsonString = JSON.stringify(obj);
        $.ajax({
                url: './listarProfesionalesEditFila.php' ,
                type: 'POST' ,
                dataType: 'html',
                data: {consulta: jsonString},
            })
            .done(function(respuesta){
                respuesta=JSON.parse(respuesta);
                
                if(respuesta[0].error==0){
                    $("#ProfesionalesEdit").empty();
                    //console.log(respuesta);

                    //$("#ProfesionalesEdit").html(respuesta);
                    var x = document.getElementById("ProfesionalesEdit");
                    c = document.createElement("option");
                    for (var i = 0; i < respuesta[0].status.length; i++) {
                        c = document.createElement("option");
                        c.text = respuesta[0].status[i].Profesionales_Nombre + " " + respuesta[0].status[i].Profesionales_Apellido;
                        c.value = respuesta[0].status[i].Profesionales_ID;
                        x.options.add(c);
                    }
                    $("#ProfesionalesEdit").val(SelectedProfessional);
                    listarDatosProfesionalEdit(SelectedEspecialidad, SelectedPractica);
                }else{
                    $('#textErrorEditFila').html(respuesta[0].status);
                    $("#editFilaModal").modal('hide');
                    $("#errorInEditFilaModal").modal();
                }


            })
            .fail(function(){
                $('#textErrorEditFila').html("Error al tratar de listar los profesionales");
                $("#editFilaModal").modal('hide');
                $("#errorInEditFilaModal").modal();
            });
      }




      function listarDatosProfesionalEdit(SelectedEspecialidad, SelectedPractica){

        var obj = { 
            Profesional_ID: $("#ProfesionalesEdit").val()
        };

        //console.log(obj);
        var jsonString = JSON.stringify(obj);
        //console.log(jsonString) ;

         $.ajax({
            url: './listarDatosProfesional.php' ,
            type: 'POST' ,
            dataType: 'html',
            data: {consulta: jsonString},
        })
        .done(function(respuesta){
            respuesta=JSON.parse(respuesta);
           //console.log(respuesta);
           if(respuesta[0].error == 0){
             var plantilla = "";
             plantilla += "<p>MP: " + respuesta[0].status[0].Profesionales_MP +"</p>";
                
          
            
            $("#datosProfesionalEdit").html(plantilla);

            $("#editFilaEspecialidades").empty();
            var x = document.getElementById("editFilaEspecialidades");
            c = document.createElement("option");

            if(respuesta[0].status.length>0){

                for (var i = 0; i < respuesta[0].status.length; i++) {
                    c = document.createElement("option");
                    c.text = respuesta[0].status[i].Especialidades_Nombre;
                    c.value = respuesta[0].status[i].Especialidades_ID;
                    x.options.add(c);
                }
                $('#editFilaPracticas').prop('disabled', false);

                if(SelectedEspecialidad){
                    $("#editFilaEspecialidades").val(SelectedEspecialidad);
                }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                var obj2 = { 
                    Profesional_ID: $("#ProfesionalesEdit").val(),
                    Especialidad_ID: $("#editFilaEspecialidades").val()
                };

                //console.log(obj2);
                var jsonString2 = JSON.stringify(obj2);

                $.ajax({
                    url: './getMeProfesional.php' ,
                    type: 'POST' ,
                    dataType: 'html',
                    data: {consulta: jsonString2},
                })
                .done(function(respuesta2){
                    respuesta2=JSON.parse(respuesta2);
                   console.log(respuesta2);
                   if(respuesta2[0].error == 0){

                        if(respuesta2[0].status[0].Especialidades_Profesionales_ME){
                            var plantilla2 = "";
                            plantilla2 += "<p>ME: " + respuesta2[0].status[0].Especialidades_Profesionales_ME +"</p>";
                        
                            $("#MeProfesionalEdit").html(plantilla2);
                        }
                        $('#MeProfesionalEdit').css('display', 'block');
                    
                   }else{
                        
                        $('#textErrorAddFila').html(respuesta2[0].status);
                        $("#addFilaModal").modal('hide');
                        $("#errorInAddFilaModal").modal();
                   }
                   

                })
                .fail(function(){
                    $('#textErrorAddFila').html("Error al tratar de obtener el ME del profesional");
                    $("#addFilaModal").modal('hide');
                    $("#errorInAddFilaModal").modal();
                });

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                $('#DivPracticaEdit').css('display', 'block');

                listarPracticasFilaEdit(SelectedPractica);

            }else{
                c.text = "Este profesional no tiene ninguna especialidad asignada";
                //x.options.add(c);
                c.selected = true;
                c.hidden = true;
                c.value = 0;
                x.options.add(c);
                $('#editFilaEspecialidades').prop('disabled', true);
                $('#DivPracticaEdit').css('display', 'none');
                $('#DivCantidadEdit').css('display', 'none');
                
                
            }

            
           }else{
                
                $('#textErrorEditFila').html(respuesta[0].status);
                $("#editFilaModal").modal('hide');
                $("#errorInEditFilaModal").modal();
           }
           

        })
        .fail(function(){
            $('#textErrorEditFila').html("Error al tratar de listar los datos del profesional");
            $("#editFilaModal").modal('hide');
            $("#errorInEditFilaModal").modal();
        });
      }




    function listarPracticasFilaEdit(SelectedPractica, SelectedTipoPlanilla_ID){








        if($("#IDTipoPlanillaEdit").val() == 1){
            //console.log("Si es uno loco")
             $.ajax({
                url: './listarConsultas.php' ,
                type: 'POST' ,
                dataType: 'html',
            })
            .done(function(respuesta){
                respuesta=JSON.parse(respuesta);
                //console.log(respuesta);
                var x = document.getElementById("editFilaPracticas");
                c = document.createElement("option");

               if(respuesta[0].error == 0){
                    var plantilla = "";

                    $("#editFilaPracticas").empty();

                    c.text = "Seleccione un tipo de Consulta";
                    //x.options.add(c);
                    c.selected = true;
                    c.hidden = true;
                    c.value = 0;
                    x.options.add(c);

                    for (var i = 0; i < respuesta[0].status.length; i++) {
                        c = document.createElement("option");
                        c.text = respuesta[0].status[i].Consultas_Nombre + " (" + respuesta[0].status[i].Consultas_Codigo + ")";
                        c.value = respuesta[0].status[i].Consultas_Codigo;
                        x.options.add(c);
                    }
                    $('#editFilaPracticas').prop('disabled', false);

                    if(SelectedPractica){
                        $("#editFilaPracticas").val(SelectedPractica);
                    }

                    $('#DivPracticaEdit').css('display', 'block');
                    $('#DivCantidadEdit').css('display', 'block');
                
               }else{

                    $('#textErrorEditFila').html(respuesta[0].status);
                    $("#editFilaModal").modal('hide');
                    $("#errorInEditFilaModal").modal();
                
               }
                   

            })
            .fail(function(){
                $('#textErrorEditFila').html("Error al tratar de listar las practicas");
                $("#editFilaModal").modal('hide');
                $("#errorInEditFilaModal").modal();
            });
            return;
        }









        var obj = { 
            Especialidad_ID: $("#editFilaEspecialidades").val()
        };

        //console.log(obj);
        var jsonString = JSON.stringify(obj);
        //console.log(jsonString) ;

         $.ajax({
            url: './listarPracticasByEspecialidad.php' ,
            type: 'POST' ,
            dataType: 'html',
            data: {consulta: jsonString},
        })
        .done(function(respuesta){
            respuesta=JSON.parse(respuesta);
            //console.log(respuesta);
            var x = document.getElementById("editFilaPracticas");
            c = document.createElement("option");

            if(respuesta[0].error == 2){
                $("#editFilaPracticas").empty();
                c.text = "Esta especialidad no tiene ninguna práctica asignada";
                c.selected = true;
                c.hidden = true;
                c.value = 0;
                x.options.add(c);
                $('#editFilaPracticas').prop('disabled', true);
                $('#DivPracticaEdit').css('display', 'block');
                $('#DivCantidadEdit').css('display', 'none');
                
                return;
            }

           if(respuesta[0].error == 0){
                var plantilla = "";

                $("#editFilaPracticas").empty();

                c.text = "Seleccione una Practica";
                //x.options.add(c);
                c.selected = true;
                c.hidden = true;
                c.value = 0;
                x.options.add(c);
                
                

                for (var i = 0; i < respuesta[0].status.length; i++) {
                    c = document.createElement("option");
                    c.text = respuesta[0].status[i].Practicas_Nombre + " (" + respuesta[0].status[i].Practicas_Codigo + ")";
                    c.value = respuesta[0].status[i].Practicas_ID;
                    x.options.add(c);
                }
                $('#editFilaPracticas').prop('disabled', false);

                if(SelectedPractica){
                    $("#editFilaPracticas").val(SelectedPractica);
                }

                $('#DivPracticaEdit').css('display', 'block');
                $('#DivCantidadEdit').css('display', 'block');
            
           }else{

                $('#textErrorEditFila').html(respuesta[0].status);
                $("#editFilaModal").modal('hide');
                $("#errorInEditFilaModal").modal();
            
           }
               

        })
        .fail(function(){
            $('#textErrorEditFila').html("Error al tratar de listar las practicas");
            $("#editFilaModal").modal('hide');
            $("#errorInEditFilaModal").modal();
        });
    }




    function calcularTotalEdit(){
        if($('#CantidadInputEdit').val() == ""){
            $('#TotalInputEdit').html("");
            $('#TotalInputEdit').css('display', 'none');
            return;
        }

        if($('#ValorUnitarioInputEdit').val() == ""){
            $('#TotalInputEdit').html("");
            $('#TotalInputEdit').css('display', 'none');
            return;
        }

        var Total = Number($('#CantidadInputEdit').val()) * Number($('#ValorUnitarioInputEdit').val());
        
        $('#TotalValorEdit').val(Total)
        $('#TotalInputEdit').html("Valor total = " + Total);
        $('#TotalInputEdit').css('display', 'block');
    }



    function updateFila(){
        if ($("#ProfesionalesEdit").val()==0) {
            //console.log("Hi");
            $('#ProfesionalesEdit').tooltip('show');
            return;
        }

        if ($("#editFilaEspecialidades").val()==0) {
            //console.log("Hi");
            $('#editFilaEspecialidades').tooltip('show');
            return;
        }

        if ($("#editFilaPracticas").val()==0) {
            //console.log("Hi");
            $('#editFilaPracticas').tooltip('show');
            return;
        }

        if ($("#CantidadInputEdit").val() == "") {
            //console.log("Hi");
            $('#CantidadInputEdit').tooltip('show');
            return;
        }

        if ($("#ValorUnitarioInputEdit").val() == "") {
            //console.log("Hi");
            $('#ValorUnitarioInputEdit').tooltip('show');
            return;
        }


         if($("#IDTipoPlanillaEdit").val() == 1){
             var obj = { 
                ID: Number($("#IDFilaEdit").val()),
                Planilla_ID: Number($("#IDPlanillaEdit").val()), 
                Especialidad_ID: Number($("#editFilaEspecialidades").val()), 
                Profesional_ID: Number($("#ProfesionalesEdit").val()),
                Consulta_ID: Number($("#editFilaPracticas").val()),
                Cantidad: Number($("#CantidadInputEdit").val()),
                Valor_Unitario: Number($("#ValorUnitarioInputEdit").val())
            };

            console.log(obj);
            var jsonString = JSON.stringify(obj);
            //console.log(jsonString) ;


             $.ajax({
                url: './updateFilaConsulta.php' ,
                type: 'POST' ,
                dataType: 'html',
                data: {consulta: jsonString},
            })
            .done(function(respuesta){
                respuesta=JSON.parse(respuesta);    
                //console.log(respuesta);
                if(respuesta[0].error == 0){
                    $("#editFilaModal").modal('hide');
                    $("#search").val("");
                    buscar_datos();
                }else{
                    $("#editFilaModal").modal('hide');
                    $("#textErrorEditFila").html(respuesta[0].status);
                    $("#errorInEditFilaModal").modal();
                }

            })
            .fail(function(){
                $('#textErrorEditFila').html("Error al tratar de agregar la fila");
                $("#editFilaModal").modal('hide');
                $("#errorInEditFilaModal").modal();
            });
            return;
         }


        var obj = { 
            ID: Number($("#IDFilaEdit").val()),
            Planilla_ID: Number($("#IDPlanillaEdit").val()), 
            Especialidad_ID: Number($("#editFilaEspecialidades").val()), 
            Profesional_ID: Number($("#ProfesionalesEdit").val()),
            Practica_ID: Number($("#editFilaPracticas").val()),
            Cantidad: Number($("#CantidadInputEdit").val()),
            Valor_Unitario: Number($("#ValorUnitarioInputEdit").val())
        };

        console.log(obj);
        var jsonString = JSON.stringify(obj);
        //console.log(jsonString) ;


         $.ajax({
            url: './updateFila.php' ,
            type: 'POST' ,
            dataType: 'html',
            data: {consulta: jsonString},
        })
        .done(function(respuesta){
            respuesta=JSON.parse(respuesta);    
            //console.log(respuesta);
            if(respuesta[0].error == 0){
                $("#editFilaModal").modal('hide');
                $("#search").val("");
                buscar_datos();
            }else{
                $("#editFilaModal").modal('hide');
                $("#textErrorEditFila").html(respuesta[0].status);
                $("#errorInEditFilaModal").modal();
            }

        })
        .fail(function(){
            $('#textErrorEditFila').html("Error al tratar de agregar la fila");
            $("#editFilaModal").modal('hide');
            $("#errorInEditFilaModal").modal();
        });

    }




    function eliminarFila(id,TipoPlanilla_ID) {
            $("#idFilaDelete").val(id);
            $("#idTipoPlanillaDeleteFila").val(TipoPlanilla_ID);
            $("#FilaAEliminar").html("¿Desea eliminar esta fila?");
            $("#deleteFilaModal").modal();
        }



     function borrarFila(){
            var obj = { 
                ID: $("#idFilaDelete").val()
            };

           // console.log(obj);
            var jsonString = JSON.stringify(obj);
            //console.log(jsonString);







            if($("#idTipoPlanillaDeleteFila").val() == 1){
                 $.ajax({
                    url: './deleteFilaConsulta.php' ,
                    type: 'POST' ,
                    dataType: 'html',
                    data: {consulta: jsonString},
                })
                .done(function(respuesta){
                    //console.log("Hola");

                    //console.log(respuesta);
                    respuesta=JSON.parse(respuesta);
                    //console.log(respuesta[0].error)
                    if(respuesta[0].error == 0){
                        $("#deleteFilaModal").modal('hide');
                        $("#search").val("");
                        buscar_datos();
                    }else{
                        $("#deleteFilaModal").modal('hide');
                        $("#textErrorDeleteFila").html(respuesta[0].status);
                        $("#errorInDeleteFilaModal").modal();
                    }


                })
                .fail(function(){
                    $("#deleteFilaModal").modal('hide');
                    $("#textErrorDeleteFila").html("Ocurrió un error con el borrado");
                    $("#errorInDeleteFilaModal").modal();
                });
                return;
            }







            $.ajax({
                url: './deleteFila.php' ,
                type: 'POST' ,
                dataType: 'html',
                data: {consulta: jsonString},
            })
            .done(function(respuesta){
                //console.log("Hola");

                //console.log(respuesta);
                respuesta=JSON.parse(respuesta);
                //console.log(respuesta[0].error)
                if(respuesta[0].error == 0){
                    $("#deleteFilaModal").modal('hide');
                    $("#search").val("");
                    buscar_datos();
                }else{
                    $("#deleteFilaModal").modal('hide');
                    $("#textErrorDeleteFila").html(respuesta[0].status);
                    $("#errorInDeleteFilaModal").modal();
                }


            })
            .fail(function(){
                $("#deleteFilaModal").modal('hide');
                $("#textErrorDeleteFila").html("Ocurrió un error con el borrado");
                $("#errorInDeleteFilaModal").modal();
            });


        }


        function cancelEditPlanilla(ObraSocial_Nombre,Periodo_Mes,Periodo_Ano,i){

            var MesesArray = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            var plantilla = "<h3>"+ ObraSocial_Nombre + " - " + MesesArray[(Periodo_Mes-1)] + "/" + Periodo_Ano +"</h3>";
            $("#Planilla_"+i).html(plantilla);

        }


    function editPlanilla(Planilla_ID, ObraSocial_ID, ObraSocial_Nombre, TipoPlanilla_ID, TipoPlanilla_Nombre, Periodo_Ano, Periodo_Mes, i){
        /*console.log("aaaaaaaaaaa");
        console.log(Planilla_ID);
        console.log(ObraSocial_ID);
        console.log(ObraSocial_Nombre);
        console.log(TipoPlanilla_ID);
        console.log(TipoPlanilla_Nombre);
        console.log(Periodo_Ano);
        console.log(Periodo_Mes);
        console.log(i);*/

       // console.log(idAnterior);

        if(idAnterior>=0){
            var MesesArray = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            var plantilla_Anterior = "<h3>"+ ObraSocial_Nombre_Anterior + " - " + MesesArray[(Periodo_Mes_Anterior-1)] + "/" + Periodo_Ano_Anterior +"</h3>";
            $("#Planilla_"+idAnterior).html(plantilla_Anterior);
        }

        /*var template = '<div class="container row align-items-end"><div class="row align-items-end col-md-10">';
        template += '<select id="SelectTipoPlanillaEditPlanilla_'+i+'"  class="form-control col-md-4" placeholder="Tipo de planilla" style="margin: 10px 0px;"></select>';
        template += '<select id="SelectObraSocialEditPlanilla_'+i+'"  class="form-control col-md-4" placeholder="Nombre de la especialidad" style="margin: 10px 0px;"></select>';
        template += '<select id="MesesEditPlanilla_'+i+'"  class="form-control col-md-4" placeholder="Nombre de la especialidad" style="margin: 10px 0px;"></select>';
        template += '<select id="AñosEditPlanilla_'+i+'"  class="form-control col-md-4" placeholder="Nombre de la especialidad" style="margin: 10px 0px;"></select>';
        template += '</div>';
        template += '<a class="col-md-1" href="#" style="margin-bottom: 15px; margin-left: 10px; color: green;" onclick="updatePlanilla('+ Planilla_ID +','+ i +',\'' + ObraSocial_Nombre + '\',' + Periodo_Mes + ',' + Periodo_Ano +');">';
        template += '<i class="fa fa-check"></i></a>';
        template += '<a class="col-md-1" href="#" style="margin-bottom: 15px; margin-left: 10px; color: red;" onclick="cancelEditPlanilla(\'' + ObraSocial_Nombre + '\',' + Periodo_Mes + ',' + Periodo_Ano + ',' + i +');">';
        template += '<i class="fa fa-close"></i></a>';
        template += '</div>';*/

        var template = '<div class="container row align-items-end" style="margin: 0 auto; padding: 0;">';
        //template += '<select id="SelectTipoPlanillaEditPlanilla_'+i+'"  class="form-control col-md-2" placeholder="Tipo de planilla" style="margin: 10px 5px 10px 0;"></select>';
        template += '<select id="SelectObraSocialEditPlanilla_'+i+'"  class="form-control col-md-2" placeholder="Nombre de la especialidad" style="margin: 10px 5px;"></select>';
        template += '<select id="MesesEditPlanilla_'+i+'"  class="form-control col-md-2" placeholder="Nombre de la especialidad" style="margin: 10px 5px;"></select>';
        template += '<select id="AñosEditPlanilla_'+i+'"  class="form-control col-md-2" placeholder="Nombre de la especialidad" style="margin: 10px 5px;"></select>';
        //template += '</div>';
        template += '<div class="col-md-2" style="margin: auto 0;">';
        template += '<a class="col-md-6" href="#" style="margin-bottom: 15px; margin-left: 10px; color: green;" onclick="updatePlanilla('+ Planilla_ID +','+ i +',\'' + ObraSocial_Nombre + '\',' + Periodo_Mes + ',' + Periodo_Ano + ',' + TipoPlanilla_ID +' );">';
        template += '<i class="fa fa-check"></i></a>';
        template += '<a class="col-md-6" href="#" style="margin-bottom: 15px; margin-left: 10px; color: red;" onclick="cancelEditPlanilla(\'' + ObraSocial_Nombre + '\',' + Periodo_Mes + ',' + Periodo_Ano + ',' + i +');">';
        template += '<i class="fa fa-close"></i></a>';
        template += '</div>';
        template += '</div>';

        $("#Planilla_"+i).html(template);

       // listarTiposPlanillasEditPlanilla(TipoPlanilla_ID,i, ObraSocial_Nombre,Periodo_Mes,Periodo_Ano);
        listarObrasSocialesEditPlanilla(ObraSocial_ID,i,ObraSocial_Nombre,Periodo_Mes,Periodo_Ano);
        listarMesesEditPlanilla(Periodo_Mes,i);
        listarAñosEditPlanilla(Periodo_Ano,i);


        idAnterior = i;
        ObraSocial_Nombre_Anterior = ObraSocial_Nombre;
        Periodo_Mes_Anterior = Periodo_Mes;
        Periodo_Ano_Anterior = Periodo_Ano;


    }


/*

    function listarTiposPlanillasEditPlanilla(TipoPlanilla_ID, j,ObraSocial_Nombre,Periodo_Mes,Periodo_Ano){
        $.ajax({
                url: './listarTiposPlanillas.php' ,
                type: 'POST' ,
                dataType: 'html',
            })
            .done(function(respuesta){
                respuesta=JSON.parse(respuesta);
                //console.log(respuesta)
                $("#SelectTipoPlanillaEditPlanilla_"+j).empty();
                //console.log(respuesta);

                $("#SelectTipoPlanillaEditPlanilla_"+j).html(respuesta);
                var x = document.getElementById("SelectTipoPlanillaEditPlanilla_"+j);
                c = document.createElement("option");
               
                for (var i = 0; i < respuesta.length; i++) {
                    c = document.createElement("option");
                    c.text = respuesta[i].Nombre;
                    c.value = respuesta[i].ID;
                    x.options.add(c);
                    //console.log(c);
                }
                $("#SelectTipoPlanillaEditPlanilla_"+j).val(TipoPlanilla_ID);
            })
            .fail(function(){
                cancelEditPlanilla(ObraSocial_Nombre,Periodo_Mes,Periodo_Ano,j);
                $('#textErrorEditPlanilla').html("Error al listar los tipos de planilla");
                $("#errorInEditPlanillaModal").modal();
            });
      }

*/


     function listarObrasSocialesEditPlanilla(ObraSocial_ID,j,ObraSocial_Nombre,Periodo_Mes,Periodo_Ano){

        var obj = { 
            ID: ObraSocial_ID
        };

        //console.log(obj);
        var jsonString = JSON.stringify(obj);


        $.ajax({
                url: './listarObrasSocialesEditPlanilla.php' ,
                type: 'POST' ,
                dataType: 'html',
                data: {consulta: jsonString},
            })
            .done(function(respuesta){
               // var esta = false;
                respuesta=JSON.parse(respuesta);
                //console.log(respuesta);
                if(respuesta[0].error == 0){
                    $("#SelectObraSocialEditPlanilla_"+j).empty();
                    //console.log(respuesta);
                    var x = document.getElementById("SelectObraSocialEditPlanilla_"+j);
                    c = document.createElement("option");
                    
                    for (var i = 0; i < respuesta[0].status.length; i++) {
                       // if(respuesta[i].ID == ObraSocial_ID){
                      //      esta = true;
                      //  }
                        c = document.createElement("option");
                        c.text = respuesta[0].status[i].Nombre;
                        c.value = respuesta[0].status[i].ID;
                        x.options.add(c);
                        //console.log(c);
                    }
                   // if(!esta){
                   //     cancelEditPlanilla();
                   //     $("#textErrorDeleteFila").html("");
                   //     $("#errorInDeleteFilaModal").modal();
                   // }
                    $("#SelectObraSocialEditPlanilla_"+j).val(ObraSocial_ID);
                }else{
                        cancelEditPlanilla(ObraSocial_Nombre,Periodo_Mes,Periodo_Ano,j);
                        $('#textErrorEditPlanilla').html(respuesta[0].status);
                        $("#errorInEditPlanillaModal").modal();
                    }
            })
            .fail(function(){
                cancelEditPlanilla(ObraSocial_Nombre,Periodo_Mes,Periodo_Ano,j);
                $('#textErrorEditPlanilla').html("Error al listar las Obras sociales");
                $("#errorInEditPlanillaModal").modal();
            
            });
      }


      function listarMesesEditPlanilla(Periodo_Mes,j){






        var MesesArray = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        $("#MesesEditPlanilla_"+j).empty();
        //x = document.getElementById("MesesEditPlanilla_"+j);
        for (var i = 0; i < 12; i++) {
            c = document.createElement("option");
            c.text = MesesArray[i];
            c.value = 1+i;
            //Comentario random
            x.options.add(c);
            //console.log(c);
        }

        //var MesActual = Number(moment().format("MM"));
        $("#MesesEditPlanilla_"+j).val(Periodo_Mes);
        //$("#Meses").val(MesActual);//
        alert("Holaaaaa");


















      }


      function listarAñosEditPlanilla(Periodo_Ano,j){

          $("#AñosEditPlanilla_"+j).empty();

          var x = document.getElementById("AñosEditPlanilla_"+j);
          var c;
          var AnoActual = Number(moment().format("YYYY"));
          //console.log(AnoActual + "," + Periodo_Ano + "," + (AnoActual-Periodo_Ano+3))
          if(AnoActual>=Periodo_Ano){
            var cantVueltas = (AnoActual - Periodo_Ano + 3);
          }else{
            var cantVueltas = (Periodo_Ano - AnoActual + 3);
            AnoActual = Periodo_Ano;
          }
          
          AnoActual++;
          for (var i = 0; i < cantVueltas; i++) {
            //console.log("Hola " +i)
              c = document.createElement("option");
              c.text = --AnoActual;
              c.value = AnoActual;
              x.options.add(c);
              //console.log(c)
          }
          $("#AñosEditPlanilla_"+j).val(Periodo_Ano);
      }



      function updatePlanilla(Planilla_ID,j, ObraSocial_Nombre,Periodo_Mes,Periodo_Ano,TipoPlanilla_In){
         var obj = { 
            ID: Planilla_ID,
            ObraSocial_ID: Number($("#SelectObraSocialEditPlanilla_"+j).val()), 
            TipoPlanilla_ID: TipoPlanilla_In, 
            Periodo_Mes: Number($("#MesesEditPlanilla_"+j).val()),
            Periodo_Ano: Number($("#AñosEditPlanilla_"+j).val())
        };

        //console.log(obj);
        var jsonString = JSON.stringify(obj);
        //console.log(jsonString) ;


         $.ajax({
            url: './updatePlanilla.php' ,
            type: 'POST' ,
            dataType: 'html',
            data: {consulta: jsonString},
        })
        .done(function(respuesta){
            respuesta=JSON.parse(respuesta);    
            //console.log(respuesta);
            if(respuesta[0].error == 0){
                buscar_datos();
            }else{
                cancelEditPlanilla(ObraSocial_Nombre,Periodo_Mes,Periodo_Ano,j);
                $("#textErrorEditPlanilla").html(respuesta[0].status);
                $("#errorInEditPlanillaModal").modal();
            }

        })
        .fail(function(){
            cancelEditPlanilla(ObraSocial_Nombre,Periodo_Mes,Periodo_Ano,j);
            $('#textErrorEditPlanilla').html("Error al tratar de editar la planilla");
            $("#errorInEditPlanillaModal").modal();

        });
      }


      

       function eliminarPlanilla(ID, ObraSocial_Nombre, TipoPlanilla_Nombre, Mes, Año, TipoPlanilla_ID) {
            $("#idPlanillaDelete").val(ID);
            $("#idTipoPlanillaDeletePlanilla").val(TipoPlanilla_ID);
            $("#PlanillaAEliminar").html("¿Desea eliminar esta planilla de "+ TipoPlanilla_Nombre +  ": " + ObraSocial_Nombre + " - " + Mes + "/" + Año + "?");
            $("#deletePlanillaModal").modal();
        }



     function borrarPlanilla(){
            var obj = { 
                ID: $("#idPlanillaDelete").val()
            };

           // console.log(obj);
            var jsonString = JSON.stringify(obj);
            //console.log(jsonString);



            if($("#idTipoPlanillaDeletePlanilla").val() == 1){
                 $.ajax({
                    url: './deletePlanillaConsulta.php' ,
                    type: 'POST' ,
                    dataType: 'html',
                    data: {consulta: jsonString},
                })
                .done(function(respuesta){
                    //console.log("Hola");

                    //console.log(respuesta);
                    respuesta=JSON.parse(respuesta);
                    //console.log(respuesta[0].error)
                    if(respuesta[0].error == 0){
                        $("#deletePlanillaModal").modal('hide');
                        
                        
                        buscar_datos();
                        $("#search").val("");
                    }else{
                        $("#deletePlanillaModal").modal('hide');
                        $("#textErrorDeletePlanilla").html(respuesta[0].status);
                        $("#errorInDeletePlanillaModal").modal();
                    }


                })
                .fail(function(){
                    $("#deletePlanillaModal").modal('hide');
                    $("#textErrorDeletePlanilla").html("Ocurrió un error con el borrado de la planilla");
                    $("#errorInDeletePlanillaModal").modal();
                });
                return;
            }



            $.ajax({
                url: './deletePlanilla.php' ,
                type: 'POST' ,
                dataType: 'html',
                data: {consulta: jsonString},
            })
            .done(function(respuesta){
                //console.log("Hola");

                //console.log(respuesta);
                respuesta=JSON.parse(respuesta);
                //console.log(respuesta[0].error)
                if(respuesta[0].error == 0){
                    $("#deletePlanillaModal").modal('hide');
                    
                    
                    buscar_datos();
                    $("#search").val("");
                }else{
                    $("#deletePlanillaModal").modal('hide');
                    $("#textErrorDeletePlanilla").html(respuesta[0].status);
                    $("#errorInDeletePlanillaModal").modal();
                }


            })
            .fail(function(){
                $("#deletePlanillaModal").modal('hide');
                $("#textErrorDeletePlanilla").html("Ocurrió un error con el borrado de la planilla");
                $("#errorInDeletePlanillaModal").modal();
            });


        }

        function exportarPDF(tablaID, tipoPlanilla, periodoMes, periodoAno, obraSocial, totalPlanilla) {
            if (!$("#tablaPlanilla_" + tablaID).length) {
                //console.log("No def");
                $("#textErrorDeletePlanilla").html("La planilla no tiene filas. Inserte una fila para exportar a PDF.");
                $("#errorInDeletePlanillaModal").modal();

                return;
            }


            var doc = new jsPDF('p', 'mm', 'a4');
            var pageSize = doc.internal.pageSize
            var pageWidth = pageSize.width ? pageSize.width : pageSize.getWidth()
            
            //var tipoPlanillaLowerCase = tipoPlanilla.toLowerCase();

            
            
            var options = {
                align: "center",
            };

            doc.setFontSize(15);
            doc.text('Centro Médico Privado Del Carmen', pageWidth/2, 25, 'center', 0);

            doc.setFontSize(12);
            doc.text('Obra social: ' + obraSocial + " - Total: $" + totalPlanilla, pageWidth-10, 10, 'right', 0);
            
            doc.setFontSize(10);
            doc.text('Elpidio González 61 - Villa Allende - Córdoba - Tel/Fax: 03543 - 430796', pageWidth/2, 30, 'center', 0);

            doc.setFontSize(12);
            doc.text('Periodo: ' + periodoMes + "/" + periodoAno, pageWidth-10, 15, 'right', 0);
            
            doc.setFontSize(15);
            doc.text('Planilla de ' + tipoPlanilla, pageWidth/2, 40, 'center', 0);

            //console.log("Tamaño: " + getStringUnitWidth("Centro Médico Del Carmen"));
            
            //var finalY = doc.lastAutoTable.finalY
            console.log("Ancho: " + pageWidth);
            
            doc.autoTable({
                html: "#tablaPlanilla_" + tablaID,
                startY: 45,
                //head: [['ID', 'Name']],
                columns: [
                    {header: 'Profesional', dataKey: 'profesional'},
                    {header: 'M.P.', dataKey: 'mp'},
                    {header: 'M.E.', datakey: 'ME'},
                    {header: 'Especialidad', datakey: 'especialidad'},
                    {header: 'Código', datakey: 'codigo'},
                    {header: 'Cantidad de consultas', datakey: 'cantConsultas'},
                    {header: 'Valor Unitario', datakey: 'valorUnitario'},
                    {header: 'Valor Total', datakey: 'valorTotal'},
                ],
                margin: {
                    right: 10,
                    left: 10,
                },
                theme: 'grid',
                headStyles: {
                    fillColor: '000000',
                },
                //useCss: true,
            })

            var finalY = doc.lastAutoTable.finalY

            doc.setFontSize(12);
            doc.text('Total planilla: $' + totalPlanilla, pageWidth-10, finalY+10, 'right', 0);

            //doc.text( 'This text is\raligned to the\rright.', 140, 100, 'center' );

            //doc.find('#options').remove();
            //source = $('#profesionalesTable')[0];

            //doc.fromHTML(source);


            //doc.setFontSize(20);
            //doc.text(35, 25, "hola buenas");

            /*var hoy = new Date();

            var date = hoy.getDate() + "-" + hoy.getMonth()+1 + "-" + hoy.getFullYear();*/

            doc.save("Planilla-" + tipoPlanilla + "-" + obraSocial + "-" + periodoMes + "-" + periodoAno + ".pdf");
        }

    

    </script>

    <?php include 'navigationBar.php' ?>

    <body>
      <div class="container">
        

        <div class="row align-items-end" style="margin: 0 auto;">
                <h1 style="margin: 20px 0px;">Planillas</h1>
                <!--<button type="button" class="btn btn-primary">Nuevo</button>-->
                <a href="#" class="text-primary ml-auto" style="margin-bottom: 13px; margin-left: 8px;" onclick="addPlanilla()">
                    <svg width="31px" height="31px" viewBox="0 0 16 16" class="bi bi-plus-circle-fill" fill="#28a745" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                    </svg>
                </a>
            </div>


        <form>
          <div class="form-group">
              <input type="text" class="form-control" id="search" aria-describedby="searchHelp" placeholder="Buscar">
              <small id="searchHelp" class="form-text text-muted">Buscar por obra social. </small>
          </div>
        </form>
       
        <?php
            require_once("db.php");
            $query = "SELECT * FROM `TiposPlanillas`";
            $rsDATA = $DBengine -> query($query);
            //$backUp = $rsDATA;
            
            $plantilla = '<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">';

             $primera = 0;

            while ($register = $rsDATA -> fetch_row()) {

                if($primera == 0){
                  $plantilla = $plantilla.'<a class="nav-item nav-link active" id="'.$register[0].'" data-toggle="tab" href="#nav-'.$register[0].'" role="tab" aria-controls="nav-'.$register[0].'" aria-selected="true">'.$register[1].'</a>';
                }else{
                  $plantilla = $plantilla.'<a class="nav-item nav-link" id="'.$register[0].'" data-toggle="tab" href="#nav-'.$register[0].'" role="tab" aria-controls="nav-'.$register[0].'" aria-selected="false">'.$register[1].'</a>';
                }
                $primera = 1;
                    
            }

            $plantilla = $plantilla.'</div>
    </nav>
    <div class="tab-content" id="nav-tabContent">';

            
            $rsDATA = $DBengine -> query($query);
            $primerContent = 0;

            while ($register = $rsDATA -> fetch_row()) {
                //echo("Hola");
                if($primerContent == 0){
                  $plantilla = $plantilla.'<div class="tab-pane fade active show" id="nav-'.$register[0].'" role="tabpanel" aria-labelledby="'.$register[0].'">'.'<div id="datos_'.$register[0].'" class="table-responsive" style="margin: 10px 0px;"></div>'.'</div>';
                }else{
                  $plantilla = $plantilla.'<div class="tab-pane fade show" id="nav-'.$register[0].'" role="tabpanel" aria-labelledby="'.$register[0].'">'.'<div id="datos_'.$register[0].'" class="table-responsive" style="margin: 10px 0px;"></div>'.'</div>';
                }
                
                 $primerContent = 1;
                    
            }

             
            $plantilla = $plantilla.'</div>';


             /*$plantilla ='<nav>
  <div class="nav nav-tabs" id="nav-tab" role="tablist">
    <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Home</a>
    <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</a>
    <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</a>
  </div>
</nav>
<div class="tab-content" id="nav-tabContent">
  <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">...</div>
  <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">...</div>
  <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">...</div>
</div>';*/

            echo($plantilla);
                    


            mysqli_close($DBengine);
        ?>





        
    
        



      </div>



 <!-- Añadir Planilla Modal -->
        <div class="modal" id="addPlanillaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Añadir Planilla</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form action="addPlanilla.php" method="POST" >
                        <div  class="modal-body">
                            <div  id="DivTipoPlanilla" class="form-group form-row align-items-end">
                                <label class="col-md-12">Tipo de planilla</label>
                                <select name="Planillas" id="Planillas" class="form-control " data-toggle="tooltip" data-placement="left" title="Debe seleccionar una planilla válida">
                                </select>
                            </div>

                            <div  id="DivObraSocial" class="form-group form-row align-items-end">
                                <label class="col-md-12">Obra Social</label>
                                <select name="ObrasSociales" id="ObrasSociales" class="form-control " data-toggle="tooltip" data-placement="left" title="Debe seleccionar una obra social">
                                </select>
                            </div>
                              
                            <div  id="DivPeriodo" class="form-group form-row" style="display: flex; align-items: center;">

                                <p class="col-md-2" style="margin-top: 10px;">Periodo:</p>

                                <select name="Meses" id="Meses" class="form-control col-md-4 " >
                                </select>

                                <p class="col-md-1" style="font-size: 25px; margin-top: 10px; margin-left: 20px;">/</p>

                                <select name="Años" id="Años" class="form-control col-md-4">
                                </select>

                            </div>
                        </div>
                    

                        <div id="DivBotones" class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success" id="addPlanilla" name="addPlanilla" onclick="guardarPlanilla()">Añadir</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Fin Añadir Planilla Modal -->
      
 <!-- Error en el Add de la Plantilla Modal  -->
        <div class="modal fade" id="errorInAddPlantillaModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabelDelete">Error</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div class="modal-body">
                            <p id="textErrorAddPlantilla"> </p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Aceptar</button>
                        </div>

                </div>
            </div>
        </div>
        <!-- Fin Error en el Add de la Plantilla Modal  -->

        <!-- Error en el Add de la fila Modal  -->
        <div class="modal fade" id="errorInAddFilaModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabelDelete">Error</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div class="modal-body">
                            <p id="textErrorAddFila"> </p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Aceptar</button>
                        </div>

                </div>
            </div>
        </div>
        <!-- Fin Error en el Add de la fila Modal  -->

        <!-- Error en el Edit de la fila Modal  -->
        <div class="modal fade" id="errorInEditFilaModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabelDelete">Error</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div class="modal-body">
                            <p id="textErrorEditFila"> </p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Aceptar</button>
                        </div>

                </div>
            </div>
        </div>
        <!-- Fin Error en el Edit de la fila Modal  -->

        <!-- Error en el Edit de la Planilla Modal  -->
        <div class="modal fade" id="errorInEditPlanillaModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabelDelete">Error</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div class="modal-body">
                            <p id="textErrorEditPlanilla"> </p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Aceptar</button>
                        </div>

                </div>
            </div>
        </div>
        <!-- Fin Error en el Edit de la Planilla Modal  -->



        <!-- Error en el Delete de la fila Modal  -->
        <div class="modal fade" id="errorInDeleteFilaModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabelDelete">Error</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div class="modal-body">
                            <p id="textErrorDeleteFila"> </p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Aceptar</button>
                        </div>

                </div>
            </div>
        </div>
        <!-- Fin Error en el Delete de la fila Modal  -->


        <!-- Error en el Delete de la planilla Modal  -->
        <div class="modal fade" id="errorInDeletePlanillaModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabelDelete">Error</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div class="modal-body">
                            <p id="textErrorDeletePlanilla"> </p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" >Aceptar</button>
                        </div>

                </div>
            </div>
        </div>
        <!-- Fin Error en el Delete de la planilla Modal  -->




        <!-- Añadir Fila Modal -->
        <div class="modal" id="addFilaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFilaModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div  class="modal-body">
                            <input type="text"  id="IDPlanilla" name="IDPlanilla" hidden> 
                            <input type="text"  id="TipoPlanilla" name="TipoPlanilla" hidden> 
                            <div  id="Profesional" class="form-group form-row align-items-end">
                                <label class="col-md-12">Profesional</label>
                                <select name="Profesionales" id="Profesionales" class="form-control " data-toggle="tooltip" data-placement="left" title="Debe seleccionar un profesional">
                                </select>
                            </div>

                            <div id="datosProfesional" class="row col-md-12" style="display: none;">
                                    
                            </div>

                            <div  id="DivEspecialidad" class="form-group form-row align-items-end" style="display: none;">
                                <label class="col-md-12">Especialidad</label>
                                <select name="addFilaEspecialidades" id="addFilaEspecialidades" class="form-control" data-toggle="tooltip" data-placement="left" title="Debe seleccionar una especialidad">
                                </select>
                            </div>
                            <div id="MeProfesional" class="row col-md-12" style="display: none;"></div>
                            <div  id="DivPractica" class="form-group form-row align-items-end" style="display: none;">
                                <label class="col-md-12">Practica</label>
                                <select name="addFilaPracticas" id="addFilaPracticas" class="form-control" data-toggle="tooltip" data-placement="left" title="Debe seleccionar una práctica">
                                </select>
                            </div>

                            <div id="DivCantidad" style="display: none;">
                                 <div  class="form-group form-row align-items-end " > 
                                    <input type="number" class="form-control col-md-4" id="CantidadInput" name="CantidadInput" placeholder="Cantidad" data-toggle="tooltip" data-placement="top" title="Debe ingresar una cantidad de consultas válida">
                                    <input type="number" class="form-control col-md-4" id="ValorUnitarioInput" name="ValorUnitarioInput" placeholder="Valor Unitario" data-toggle="tooltip" data-placement="top" title="Debe ingresar un valor unitario">
                                    <div id="TotalInput" class="col-md-4" style="display: none;"></div> 
                                    <input type="text"  id="TotalValor" name="TotalValor" hidden>
                                     
                                </div> 
                                
                            </div>


                                                    
                            
                        </div>
                    

                        <div id="DivBotones" class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success" id="addFila" name="addFila" onclick="guardarFila()">Añadir</button>
                        </div>

                </div>
            </div>
        </div>
        <!-- Fin Añadir Fila Modal -->





         <!-- Edit Fila Modal -->
        <div class="modal" id="editFilaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editFilaModalLabel">Modificar Fila</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div  class="modal-body">
                            <input type="text"  id="IDPlanillaEdit" name="IDPlanillaEdit" hidden> 
                            <input type="text"  id="IDFilaEdit" name="IDFilaEdit" hidden> 
                            <input type="text"  id="IDTipoPlanillaEdit" name="IDTipoPlanillaEdit" hidden> 
                            <div  id="ProfesionalEdit" class="form-group form-row align-items-end">
                                <label class="col-md-12">Profesional</label>
                                <select name="ProfesionalesEdit" id="ProfesionalesEdit" class="form-control " data-toggle="tooltip" data-placement="left" title="Debe seleccionar un profesional">
                                </select>
                            </div>

                            <div id="datosProfesionalEdit" class="row col-md-12">
                                    
                            </div>

                            <div  id="DivEspecialidadEdit" class="form-group form-row align-items-end">
                                <label class="col-md-12">Especialidad</label>
                                <select name="editFilaEspecialidades" id="editFilaEspecialidades" class="form-control" data-toggle="tooltip" data-placement="left" title="Debe seleccionar una especialidad">
                                </select>
                            </div>
                             <div id="MeProfesionalEdit" class="row col-md-12" style="display: none;"></div>
                            <div  id="DivPracticaEdit" class="form-group form-row align-items-end">
                                <label class="col-md-12">Practica</label>
                                <select name="editFilaPracticas" id="editFilaPracticas" class="form-control" data-toggle="tooltip" data-placement="left" title="Debe seleccionar una práctica">
                                </select>
                            </div>

                            <div id="DivCantidadEdit">
                                 <div  class="form-group form-row align-items-end " > 
                                    <input type="number" class="form-control col-md-4" id="CantidadInputEdit" name="CantidadInputEdit" placeholder="Cantidad" data-toggle="tooltip" data-placement="top" title="Debe ingresar una cantidad de consultas válida">
                                    <input type="number" class="form-control col-md-4" id="ValorUnitarioInputEdit" name="ValorUnitarioInputEdit" placeholder="Valor Unitario" data-toggle="tooltip" data-placement="top" title="Debe ingresar un valor unitario">
                                    <div id="TotalInputEdit" class="col-md-4"></div> 
                                    <input type="text"  id="TotalValorEdit" name="TotalValorEdit" hidden>
                                     
                                </div> 
                                
                            </div>


                                                    
                            
                        </div>
                    

                        <div id="DivBotonesEdit" class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-success" id="editFila" name="editFila" onclick="updateFila()">Guardar Cambios</button>
                        </div>

                </div>
            </div>
        </div>
        <!-- Fin Edit Fila Modal -->



<!-- Eliminar Fila Modal -->
        <div class="modal fade" id="deleteFilaModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Eliminar práctica</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div class="modal-body">
                            <input type="hidden" id="idFilaDelete" name="idPractica">
                            <input type="hidden" id="idTipoPlanillaDeleteFila" name="idTipoPlanillaDeleteFila">
                            <p id="FilaAEliminar"> </p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                            <button type="button" class="btn btn-danger" name="deleteFila" onclick="borrarFila()">Sí</button>
                        </div>
                </div>
            </div>
        </div>
        <!-- Fin Eliminar Fila Modal -->


        <!-- Eliminar Planilla Modal -->
        <div class="modal fade" id="deletePlanillaModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Eliminar planilla</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                        <div class="modal-body">
                            <input type="hidden" id="idPlanillaDelete" name="idPractica">
                            <input type="hidden" id="idTipoPlanillaDeletePlanilla" name="idTipoPlanillaDeletePlanilla">
                            <p id="PlanillaAEliminar"> </p>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                            <button type="button" class="btn btn-danger" name="deletePlanilla" onclick="borrarPlanilla()">Sí</button>
                        </div>
                </div>
            </div>
        </div>
        <!-- Fin Eliminar Planilla Modal -->

         





       <script
              src="https://code.jquery.com/jquery-3.5.1.js"
              integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
              crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
        <script src="./buscarPlanilla.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js" integrity="sha512-LGXaggshOkD/at6PFNcp2V2unf9LzFq6LE+sChH7ceMTDP0g2kn6Vxwgg7wkPP7AAtX+lmPqPdxB47A0Nz0cMQ==" crossorigin="anonymous"></script>

        <script>


            $(document).on('click', function (e) {

               // $('[data-toggle="popover"]').each(function () {
                    // hide any open popovers when the anywhere else in the body is clicked
                    if (!$("#addPlanilla").is(e.target)) {
                       // $(this).popover('hide');
                       //console.log("hola");
                        $('#Planillas').tooltip('hide');
                        $('#ObrasSociales').tooltip('hide');
                        //a=true;
                    }

                    if (!$("#addFila").is(e.target)) {

                        $('#Profesionales').tooltip('hide');
                        $('#addFilaEspecialidades').tooltip('hide');
                        $('#addFilaPracticas').tooltip('hide');
                        $('#CantidadInput').tooltip('hide');
                        $('#ValorUnitarioInput').tooltip('hide');
                    }

                    if (!$("#editFila").is(e.target)) {

                        $('#ProfesionalesEdit').tooltip('hide');
                        $('#editFilaEspecialidades').tooltip('hide');
                        $('#editFilaPracticas').tooltip('hide');
                        $('#CantidadInputEdit').tooltip('hide');
                        $('#ValorUnitarioInputEdit').tooltip('hide');
                    }







                    
                   
            });



             $(function () {
              $('[data-toggle="tooltip"]').tooltip({
                trigger: 'manual'
              })
            })



             $("#Profesionales").on('change', function() {
                    
                $("#MeProfesional").html("");
                var obj = { 
                    Profesional_ID: $("#Profesionales").val()
                };

                //console.log(obj);
                var jsonString = JSON.stringify(obj);
                //console.log(jsonString) ;

                 $.ajax({
                    url: './listarDatosProfesional.php' ,
                    type: 'POST' ,
                    dataType: 'html',
                    data: {consulta: jsonString},
                })
                .done(function(respuesta){
                    respuesta=JSON.parse(respuesta);
                   console.log(respuesta);
                   if(respuesta[0].error == 0){
                    var plantilla = "";
                    plantilla += "<p>MP: " + respuesta[0].status[0].Profesionales_MP +"</p>";
                    
                    $("#datosProfesional").html(plantilla);

                    $("#addFilaEspecialidades").empty();
                    var x = document.getElementById("addFilaEspecialidades");
                    c = document.createElement("option");

                    if(respuesta[0].status.length>0){

                        for (var i = 0; i < respuesta[0].status.length; i++) {
                            c = document.createElement("option");
                            c.text = respuesta[0].status[i].Especialidades_Nombre;
                            c.value = respuesta[0].status[i].Especialidades_ID;
                            x.options.add(c);
                        }
                        $('#addFilaPracticas').prop('disabled', false);

                        if(respuesta[0].status[0].Especialidades_Profesionales_ME){
                            var plantilla2 = "";
                            plantilla2 += "<p>ME: " + respuesta[0].status[0].Especialidades_Profesionales_ME +"</p>";
                        
                            $("#MeProfesional").html(plantilla2);
                        }

                    }else{
                        c.text = "Este profesional no tiene ninguna especialidad asignada";
                        //x.options.add(c);
                        c.selected = true;
                        c.hidden = true;
                        c.value = 0;
                        x.options.add(c);
                        $('#addFilaEspecialidades').prop('disabled', true);
                    }

                    
                    
                    $('#datosProfesional').css('display', 'block');
                    $('#DivEspecialidad').css('display', 'block');
                    listarPracticasFila();
                    
                   }else{
                        
                        $('#textErrorAddFila').html(respuesta[0].status);
                        $("#addFilaModal").modal('hide');
                        $("#errorInAddFilaModal").modal();
                   }
                   

                })
                .fail(function(){
                    $('#textErrorAddFila').html("Error al tratar de listar los datos del profesional");
                    $("#addFilaModal").modal('hide');
                    $("#errorInAddFilaModal").modal();
                });

            });



            $("#addFilaEspecialidades").on('change', function() {


                $("#MeProfesional").html("");


                var obj = { 
                    Profesional_ID: $("#Profesionales").val(),
                    Especialidad_ID: $("#addFilaEspecialidades").val()
                };

                //console.log(obj);
                var jsonString = JSON.stringify(obj);

                $.ajax({
                    url: './getMeProfesional.php' ,
                    type: 'POST' ,
                    dataType: 'html',
                    data: {consulta: jsonString},
                })
                .done(function(respuesta){
                    respuesta=JSON.parse(respuesta);
                   console.log(respuesta);
                   if(respuesta[0].error == 0){

                        if(respuesta[0].status[0].Especialidades_Profesionales_ME){
                            var plantilla2 = "";
                            plantilla2 += "<p>ME: " + respuesta[0].status[0].Especialidades_Profesionales_ME +"</p>";
                        
                            $("#MeProfesional").html(plantilla2);
                        }
                    
                   }else{
                        
                        $('#textErrorAddFila').html(respuesta[0].status);
                        $("#addFilaModal").modal('hide');
                        $("#errorInAddFilaModal").modal();
                   }
                   

                })
                .fail(function(){
                    $('#textErrorAddFila').html("Error al tratar de obtener el ME del profesional");
                    $("#addFilaModal").modal('hide');
                    $("#errorInAddFilaModal").modal();
                });





                
                    
                listarPracticasFila();

            });

            $("#CantidadInput").on('keyup', function() {
                    
                calcularTotal();

            });

            $("#ValorUnitarioInput").on('keyup', function() {
                    
                calcularTotal();

            });

            $("#CantidadInput").on('change', function() {
                    
                calcularTotal();

            });

            $("#ValorUnitarioInput").on('change', function() {
                    
                calcularTotal();

            });




             $("#ProfesionalesEdit").on('change', function() {
                    $("#MeProfesionalEdit").html("");

                listarDatosProfesionalEdit(null, null);

            });

        $("#editFilaEspecialidades").on('change', function() {


            $("#MeProfesionalEdit").html("");
             var obj2 = { 
                    Profesional_ID: $("#ProfesionalesEdit").val(),
                    Especialidad_ID: $("#editFilaEspecialidades").val()
                };

                //console.log(obj2);
                var jsonString2 = JSON.stringify(obj2);

                $.ajax({
                    url: './getMeProfesional.php' ,
                    type: 'POST' ,
                    dataType: 'html',
                    data: {consulta: jsonString2},
                })
                .done(function(respuesta2){
                    respuesta2=JSON.parse(respuesta2);
                   console.log(respuesta2);
                   if(respuesta2[0].error == 0){

                        if(respuesta2[0].status[0].Especialidades_Profesionales_ME){
                            var plantilla2 = "";
                            plantilla2 += "<p>ME: " + respuesta2[0].status[0].Especialidades_Profesionales_ME +"</p>";
                        
                            $("#MeProfesionalEdit").html(plantilla2);
                        }
                        $('#MeProfesionalEdit').css('display', 'block');
                    
                   }else{
                        
                        $('#textErrorAddFila').html(respuesta2[0].status);
                        $("#addFilaModal").modal('hide');
                        $("#errorInAddFilaModal").modal();
                   }
                   

                })
                .fail(function(){
                    $('#textErrorAddFila').html("Error al tratar de obtener el ME del profesional");
                    $("#addFilaModal").modal('hide');
                    $("#errorInAddFilaModal").modal();
                });


                    
                listarPracticasFilaEdit(null);

            });


            $("#CantidadInputEdit").on('keyup', function() {
                    
                calcularTotalEdit();

            });

            $("#ValorUnitarioInputEdit").on('keyup', function() {
                    
                calcularTotalEdit();

            });

            $("#CantidadInputEdit").on('change', function() {
                    
                calcularTotalEdit();

            });

            $("#ValorUnitarioInputEdit").on('change', function() {
                    
                calcularTotalEdit();

            });




        </script>


    </body>

</html>

