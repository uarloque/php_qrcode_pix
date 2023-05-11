<?php
    $emailSubject = "Compra de Token Plata via PIX";
    $emailUser = $_POST['emailUser'];
    $headers = "From: noreply@plata.ie";
  
    $valor_pix = $_POST['valorpix'];
    $chave_pix = "pix@plata.ie";
    $beneficiario_pix = "Adam Warlock Soares";
    $cidade_pix = "São Vicente";
    $web3wallet = $_POST['web3wallet'];
    $identificador = $_POST['identificador'];
    $gerar_qrcode = true;
    $email = $_POST["email"];
    //$confirmemail = $_POST["confirmemail"];
    $PLTwanted = $_POST["PLTwanted"];
    
    date_default_timezone_set('UTC');
    $Expdate = date("H:i:s T d/m/Y");
    $Expdate = strtotime($Expdate);
    $Expdate = strtotime("+15 minute", $Expdate);
    $TXTdate = "Orçamento válido até ".date("H:i:s T d/m/Y",$Expdate);

    $emailMessage = 
                    "User's email: ".$emailUser."\n"."\n".
                    "Valor Aguardado (BRL) : ".$valor_pix."\n".
                    "Tokens Plata Previstas (PLT) : ".$PLTwanted."\n".
                    "Web3 Wallet : ". $web3wallet."\n".
                    "Chain ID : Polygon (137)"."\n".
                    "Identificador : ". $identificador."\n".
                    "Orçamento Válido Até : ". date("H:i:s T d/m/Y",$Expdate)
                  ;

  mail($emailUser, $emailSubject, $emailMessage, $headers);
  mail('salesdone@plata.ie', $emailSubject, $emailMessage, $headers);
  mail('uarloque@live.com', $emailSubject, $emailMessage, $headers);
  
 //echo "<br><table style='width:95%;' class='center'><tr><td>";
 echo "<br>";
 echo "  Email : ". $_POST['emailUser'] . "<br>";
 echo "  Valor Aguardado (BRL) : ". $_POST['valorpix'] . "<br>";
 echo "  Tokens Plata Previstas (PLT) : ". $_POST['PLTwanted'] . "<br>";
 echo "  Carteira Web3  : <br>";
 echo "  " . $_POST['web3wallet'] . "<br>";
 echo "  Rede ID : Polygon (137)" . "<br>";
 echo "  Identificador : " .$_POST['identificador'] . "<br>";
 echo "  Orçamento Válido Até : " . $_POST['TXTdate'] . "<br>";
 //echo "</tr></td></table>";
 
    include "phpqrcode/qrlib.php"; 
   include "funcoes_pix.php";
   $px[00]="01"; //Payload Format Indicator, Obrigatório, valor fixo: 01
   // Se o QR Code for para pagamento único (só puder ser utilizado uma vez), descomente a linha a seguir.
   $px[01]="12"; //Se o valor 12 estiver presente, significa que o BR Code só pode ser utilizado uma vez. 
   $px[26][00]="br.gov.bcb.pix"; //Indica arranjo específico; “00” (GUI) obrigatório e valor fixo: br.gov.bcb.pix
   $px[26][01]=$chave_pix;
   if (!empty($descricao)) {
      /* 
      Não é possível que a chave pix e infoAdicionais cheguem simultaneamente a seus tamanhos máximos potenciais.
      Conforme página 15 do Anexo I - Padrões para Iniciação do PIX  versão 1.2.006.
      */
      $tam_max_descr=99-(4+4+4+14+strlen($chave_pix));
      if (strlen($descricao) > $tam_max_descr) {
         $descricao=substr($descricao,0,$tam_max_descr);
      }
      $px[26][02]=$descricao;
   }
   $px[52]="0000"; //Merchant Category Code “0000” ou MCC ISO18245
   $px[53]="986"; //Moeda, “986” = BRL: real brasileiro - ISO4217
   if ($valor_pix > 0) {
      // Na versão 1.2.006 do Anexo I - Padrões para Iniciação do PIX estabelece o campo valor (54) como um campo opcional.
      $px[54]=$valor_pix;
   }
   $px[58]="BR"; //“BR” – Código de país ISO3166-1 alpha 2
   $px[59]="Adam Warlock Soares"; //Nome do beneficiário/recebedor. Máximo: 25 caracteres.
   $px[60]=$cidade_pix; //Nome cidade onde é efetuada a transação. Máximo 15 caracteres.
   $px[62][05]=$identificador;
//   $px[62][50][00]="BR.GOV.BCB.BRCODE"; //Payment system specific template - GUI
//   $px[62][50][01]="1.2.006"; //Payment system specific template - versão
   $pix=montaPix($px);
   $pix.="6304"; //Adiciona o campo do CRC no fim da linha do pix.
   $pix.=crcChecksum($pix); //Calcula o checksum CRC16 e acrescenta ao final.
   $linhas = round(strlen($pix)/120)+1;
   ?>
    <link rel="stylesheet" href="card-style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <style>
        .invisibled {
            font-size: 0px;
            text-align: center;
            border-style: none;
            resize:none;
            width: 0px;
            height: 0px;
        }
        table,td,th{
            border: 1px solid black;
        }
        
        .center {
            margin-left: auto;
            margin-right: auto;
        }
        
    </style>
<script>
    function copiar() {
    var copyText = document.getElementById("brcodepix");
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */
    document.execCommand("copy");
    alert("Código PIX Copiado!");
}

</script>
   <div class="card">
   <center><h3>Pedido Gerado</h3></center>
   <center>Tokens Plata Reservados. Aguardando Pagamento</center>
   <div class="row" style ="">
    <div>
        <textarea id="brcodepix" class="invisibled" rows="<?= $linhas; ?>" cols="130"><?= $pix;?></textarea>
    </div>

   </div>
   </div>
   <center>
   <?php
   ob_start();
   QRCode::png($pix, null,'M',5);
   $imageString = base64_encode( ob_get_contents() );
   ob_end_clean();
   // Exibe a imagem diretamente no navegador codificada em base64.
   echo '<img src="data:image/png;base64,' . $imageString . '"></center>';
?>
    <br>
    <center><img src="https://www.plata.ie/images/pix-full-logo.svg"></center>
    <br>
    <div>
    <center><button type="button" id="clip_btn" class="buttonBuyNow" data-toggle="tooltip" data-placement="top" onclick="copiar()">Pix Copia e Cola<i class="fas fa-clipboard"></i></button></center>
    </div>

<div><center><br>
Abra o aplicativo do seu banco no celular.<br>
Selecione a opção de pagar com PIX, ler QR code.<br>
Após o pagamento, você receberá Tokens Plata através da Polygon Chain (137).<br>
</center></div>
