<?php 
    include 'header.php'; 
    include 'db.php'; 


    //Logica per impaginazione
    $perPagina = 5;  // n elementi mostrati per pagina
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $perPagina;


    //QUERY PER ESTRARRE DATI PER SELECT DROPDOWN Clienti e Destinazioni
    $clienti = $conn->query("SELECT id, nome, cognome FROM clienti");
    $destinazioni = $conn->query("SELECT id, citta, paese FROM destinazioni");



    //LOGICA DI AGGIUNTA
    //chiamata POST che prende il gancio del bottone aggiugi del form, prendendo i valori inseriti nei vari campi
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['aggiungi'])){

        //Preparo lo stato stmt -> statement 
        $stmt = $conn->prepare("INSERT INTO prenotazioni (id_cliente, id_destinazione, acconto, assicurazione) 
                                VALUES  (?, ?, ?, ?)");
        //Binding dei parametri e tipizzo
        $stmt->bind_param("iiii", $_POST['id_cliente'], $_POST['id_destinazione'],$_POST['acconto'],$_POST['assicurazione']);
        
        //eseguo lo statement
        $stmt->execute();

        //Redirect post inserimento, che grazie alla funzione setTimeout di Js, ritarderà il redirect 
        //verso le prenotazioni
        echo "<div class='alert alert-info'>Prenotazione Aggiunta correttamente</div>";
        echo "
        
                <script>

                    setTimeout(function () {

                        window.location.href = 'prenotazioni.php'

                    }, 2500);

                </script>
        

             ";
        exit;

    }





    //LOGICA DI MODIFICA
    $prenotazione_modifica = null;

    if (isset($_GET['modifica'])){


        $res = $conn->query("SELECT * FROM prenotazioni WHERE id = " . intval($_GET['modifica']));

        $prenotazione_modifica = $res->fetch_assoc();

    }





    //MODIFICA DEL DATO, SALVATAGGIO 
    if($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['salva_modifica'])){


        $assicurazione = intval($_POST['assicurazione']);//valorizza se vero o falso la presenza assicurazione

        //PREPARE
        $stmt = $conn->prepare("UPDATE prenotazioni SET id_cliente=?, id_destinazione=?,  acconto=?, assicurazione=? WHERE id=?");
        //BINDING
        $stmt->bind_param("iiiii" ,$_POST['id_cliente'],$_POST['id_destinazione'],$_POST['acconto'], $assicurazione, $_POST['id']);
        //ESECUZIONE QUERY
        $stmt->execute();
        //messaggio

        //Redirect post inserimento, che grazie alla funzione setTimeout di Js, ritarderà il redirect 
        //verso le prenotazioni
        echo "<div class='alert alert-info'>Prenotazione Modificata correttamente</div>";
        echo "
        
                <script>

                    setTimeout(function () {

                        window.location.href = 'prenotazioni.php'

                    }, 2500);

                </script>
        
             ";

        exit;
    }





    //CANCELLAZIONE CLIENTE
    if(isset($_GET['elimina'])){

        $id = intval($_GET['elimina']);
        $conn->query("DELETE FROM prenotazioni WHERE id = $id");

        echo "<div class='alert alert-info'>Prenotazione Cancellata correttamente</div>";
    }


    //vado a conteggiare il totale dei clienti con query
    $total = $conn->query("SELECT COUNT(*) as t FROM prenotazioni")->fetch_assoc()['t'];
    $totalPages = ceil($total / $perPagina); // il numero di pagine della navigazione

    //QUERY PER ordinare i dati in modo DECRESCENTE IMPAGINATI PER valore di "$perPagina" 
    //$result = $conn->query("SELECT * FROM prenotazioni ORDER BY id ASC LIMIT $perPagina OFFSET $offset");



    //QUERY ASSOCIAZIONE JOIN TRA LE DUE TABELLE 

    $stmt = $conn->prepare("SELECT p.id, c.nome, c.cognome, d.citta, d.paese, p.data_prenotazione, p.acconto, p.assicurazione
                            FROM prenotazioni p
                            JOIN clienti c ON p.id_cliente = c.id
                            JOIN destinazioni d ON p.id_destinazione = d.id
                            ORDER BY p.id DESC LIMIT ? OFFSET ?
                            ");
    $stmt->bind_param("ii", $perPagina, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

?>





<h2>Prenotazioni</h2>

    <!--Form-->
    <div class="card mb-4">
        <div class="card-body">


            <form action="" method="POST">


                <?php if($prenotazione_modifica): ?>
                
                    <input type="hidden" name="id" value="<?= $prenotazione_modifica['id'] ?>">

                <?php endif; ?>

                <div class="row g-3">
                    
                    <div class="col-md-6">

                        <label style="font-weight: 600;" for="">Cliente: </label>
                        <select name="id_cliente" class="form-select" required>

                            <option value="">Seleziona il cliente</option>
                            <?php while ($c = $clienti->fetch_assoc()) : ?>

                                <option value="<?= $c['id'] ?>"

                                    <?= ($prenotazione_modifica && $prenotazione_modifica['id_cliente'] == $c['id']) ? 'selected' : '' ?>>
                                    <?= $c['nome'] . ' ' . $c['cognome'] ?>
                                    
                                </option>

                            <?php endwhile; ?>
                        </select>
                        
                    </div>
                    
                    <div class="col-md-6">
                        <label style="font-weight: 600;" for="">Destinazione: </label>
                        <div class="">
                            <select name="id_destinazione" class="form-control" onfocus='this.size=3;' onblur='this.size=1;' onchange='this.size=1; this.blur();' required>

                                <option value="">Seleziona Destinazione</option>
                                <?php while ($d = $destinazioni->fetch_assoc()) : ?>

                                     <option value="<?= $d['id'] ?>"

                                        <?= ($prenotazione_modifica && $prenotazione_modifica['id_destinazione'] == $d['id']) ? 'selected' : '' ?>>
                                        <?= $d['citta'] . ' ' . $d['paese'] ?>
                                    
                                    </option>


                                <?php endwhile; ?>
                            </select>
                        </div>    


                    </div>
                    
                   
                    
             

                     <div class="col-md-6">
                        <label style="font-weight: 600;" for="">Acconto: </label>
                        <input type="number" name="acconto" class="form-control" placeholder="" 
                        
                        value="<?= $prenotazione_modifica['acconto'] ?? ''?>"
                        
                        required>
                    </div>

                
                    


                    <div class="col-md-2">
                        <label style="font-weight: 600;" for="">Assicurazione: </label>
                        
                        <!--Logica ternaria dato assicurazione booleano/ tinyInt su Mysql trattato come int in php-->
                        
                        <select name="assicurazione" id="" class="form-select" required>

                            <option value="1"<?= ($prenotazione_modifica && $prenotazione_modifica['assicurazione']) == 1 ? 'selected' : '' ?>>SI</option>
                            <option value="0"<?= ($prenotazione_modifica && $prenotazione_modifica['assicurazione']) == 0 ? 'selected' : '' ?>>NO</option>

                        </select>
                        
                      
                    </div>
                    
                    
                    <div class="col-md-12">
                        
                        <!--Pulsante AGGIUNGI-->
                        <button 
                            name="<?= $prenotazione_modifica ? 'salva_modifica' : 'aggiungi' ?>" 
                            class="btn <?= $prenotazione_modifica ? 'btn-warning' : 'btn-success' ?>" 
                            type="submit">
                            <?= $prenotazione_modifica ? 'Salva' : 'Aggiungi' ?>
                        </button>

                        <!--Pulsante ANNULLA-->
                        <?php if ($prenotazione_modifica) : ?>

                            <a href="prenotazioni.php" class="btn btn-secondary ms-2">Annulla</a>

                        <?php endif;?>
                    </div>

                </div>
            </form>
        </div>
    </div>




    <!--Tabella-->
    <table class="table table-striped">

        <thead>
            <!--Intestazione tabella-->
            <tr>

                <th>ID</th>
                <th>Cliente</th>
                <th>Destinazione</th>
                <th>Data di Prenotazione</th>
                <th>Acconto</th>
                <th>Assicurazione</th>
                <th>Azioni</th>

            </tr>

        </thead>
        <!--Corpo tabella-->
        <tbody>

            <?php while ($row = $result->fetch_assoc()) : ?>
                
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['nome'] . ' ' . $row['cognome'] ?></td>
                    <td><?= $row['citta'] ?></td>
                    <td><?= $row['data_prenotazione'] ?></td>
                    <td><?= $row['acconto'] ?></td>
                    <td><?= $row['assicurazione'] == 1 ? 'Presente' : 'Non presente' ?></td>
                    <td>

                        <a class="btn btn-sm btn-warning" href="?modifica=<?= $row['id']  ?>">Modifica</a>
                        <a class="btn btn-sm btn-danger" href="?elimina=<?= $row['id']  ?>" onclick="return confirm ('Sicuro?')">Elimina</a>


                    </td>
                </tr>


            <?php endwhile; ?>

        </tbody>

    </table>



    <!--Paginazione-->
    <nav>

        <ul class="pagination pagination_personal">


            <?php for($i = 1; $i <= $totalPages; $i++ ) : ?>

                <li class="page-item  <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link pagination_personal" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>   

            <?php endfor; ?>



        </ul>
    </nav>

<?php include 'footer.php'; ?>