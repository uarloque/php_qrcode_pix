<?php
// define variables and set to empty values

$emailSubject = "";
$ComboBugLink = "";
$ComboBugOS = "";
$ComboBugBrowser = "";
$emailUserReporter = "";
$TextAreaComment = "";
$headers = "";
$emailMessage = "";
$Expdate = "Orçamento Expira a cada 10 minutos.";

if(isset($_POST['submit']) )
 {
  $emailSubject = "Compra de Token Plata via PIX";
  $ComboBugOS = "teste1";
  $ComboBugBrowser = "123";
  $emailUser = $_POST['emailUser'];
  $TextAreaComment = "teste3";
  $headers = "From: noreply@plata.ie";
  
    $valor_pix = $_POST['valorpix'];
    $chave_pix = "pix@plata.ie";
    $beneficiario_pix = "Adam Warlock Soares";
    $cidade_pix = "São Vicente";
    $web3wallet = $_POST['web3wallet'];
    $identificador = (rand(100,999));
    $gerar_qrcode = true;
    //$email = $_POST["email"];
    $confirmemail = $_POST["confirmemail"];
    $PLTwanted = $_POST["PLTwanted"];
    date_default_timezone_set('UTC');
    $Expdate = "Orçamento válido até: ".date("H:i:s T d/m/Y");
    
    $emailMessage = 
                    "User's email: ".$emailUser."\n"."\n".
                    "Pagamento Aguardado (BRL) : ".$valor_pix."\n".
                    "Tokens Plata Previstas (PLT) : ".$PLTwanted."\n".
                    "Web3 Wallet : ". $web3wallet."\n".
                    "Chain ID : Polygon (137)"."\n".
                    "Identificador : ". $identificador
                  ;
    
  
  mail($emailUser, $emailSubject, $emailMessage, $headers);


  //header("location: https://www.plata.ie/");

 }
else { 
    $gerar_qrcode=false;

}

?>
<!doctype html>

<script>
    function BRLexec() {
        let textBRL = Number(document.getElementById("valorpix").value);
        document.getElementById("PLTwanted").value = (textBRL/_PLTBRL).toFixed(4);

    }

    function PLTexec() {
        let textPLT = Number(document.getElementById("PLTwanted").value);
        document.getElementById("valorpix").value = (textPLT*_PLTBRL).toFixed(4);
    }


    
</script>
<html lang="pt-br">
<head>
<title>Gerador de QR Code do PIX</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Gerador gratuito de QR Code e BR Code do Pix. Gere o seu QR Code ou a linha digitável do Pix Copia e Cola.">
<meta name="keywords" content="pix, qrcode pix, qr code, br code, brcode pix, pix copia e cola" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
<script src="https://kit.fontawesome.com/0f8eed42e7.js" crossorigin="anonymous"></script>
<script>
function copiar() {
  var copyText = document.getElementById("brcodepix");
  copyText.select();
  copyText.setSelectionRange(0, 99999); /* For mobile devices */
  document.execCommand("copy");
  document.getElementById("clip_btn").innerHTML='<i class="fas fa-clipboard-check"></i>';
}
function reais(v){
    v=v.replace(/\D/g,"");
    v=v/100;
    v=v.toFixed(2);
    return v;
}
function mascara(o,f){
    v_obj=o;
    v_fun=f;
    setTimeout("execmascara()",1);
}
function execmascara(){
    v_obj.value=v_fun(v_obj.value);
}
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
</script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-E6M96X7Y2Y"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-E6M96X7Y2Y');
</script>
<style>
a {text-decoration: none;} 
p {text-align: center;}
</style>
</head>
<body>
<?php
/*
# Exemplo de uso do php_qrcode_pix com descrição dos campos
#
# Desenvolvido em 2020 por Renato Monteiro Batista - http://renato.ovh
#
# Este código pode ser copiado, modificado, redistribuído
# inclusive comercialmente desde que mantidos a refereência ao autor.
*/
if ($gerar_qrcode){
   include "phpqrcode/qrlib.php"; 
   include "funcoes_pix.php";
   $px[00]="01"; //Payload Format Indicator, Obrigatório, valor fixo: 01
   // Se o QR Code for para pagamento único (só puder ser utilizado uma vez), descomente a linha a seguir.
   //$px[01]="12"; //Se o valor 12 estiver presente, significa que o BR Code só pode ser utilizado uma vez. 
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
   
    <style>
        .invisibled {
            font-size: 0px;
            text-align: center;
            border-style: none;
            resize:none;
            width: 0px;
            height: 0px;
        }
    </style>

   <div class="card">
   <center><h3>Linha do Pix</h3></center>
   <div class="row" style ="">
    <div>
        <textarea class="invisibled" id="brcodepix" rows="<?= $linhas; ?>" cols="130" onclick="copiar()"><?= $pix;?></textarea>
    </div>
      <div>
      <p><button type="button" id="clip_btn" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Copiar código pix" onclick="copiar()"><i class="fas fa-clipboard"></i></button></p>
      </div>
   </div>
   </div>
   <p>
   <img src="logo_pix.png"><br>
   <?php
   ob_start();
   QRCode::png($pix, null,'M',5);
   $imageString = base64_encode( ob_get_contents() );
   ob_end_clean();
   // Exibe a imagem diretamente no navegador codificada em base64.
   echo '<img src="data:image/png;base64,' . $imageString . '"></p>';
}
?>
<center><h3>Buy Plata with PIX</h3></center>
<div class="card">
<div class="card-body">
<form method="post" action="">
   <?php $chave_pix = "pix@plata.ie";?>
   <?php $beneficiario_pix = "Adam Warlock Soares";?>
   <?php $cidade_pix = "São Vicente";?>

   <div class="row row-cols-lg-auto g-3 align-items-center">
      <label for="valor" class="form-label">Valor a pagar (BRL):</label>
      <input type="text" id="valorpix" name="valorpix" size="15" autocomplete="off" maxlength="13" min="0.01" value="<?= $_POST["valorpix"];?>" onkeyup="BRLexec()" onkeypress="BRLexec()" onclick="this.select();" onkeypress="mascara(this,reais)" required>
   </div>
   <div class="row row-cols-lg-auto g-3 align-items-center">
      <label for="PLTwanted" class="form-label">Tokens Plata previstas (PLT):</label>
      <input type="text" id="PLTwanted" name="PLTwanted" size="15" autocomplete="off" maxlength="13" value="<?= $_POST["PLTwanted"];?>" onkeyup="PLTexec()" onkeypress="PLTexec()" onclick="this.select();" required>
      <label for="PLTwanted" class="form-label"><?php echo $Expdate;?></label>
   </div>
    <div class="row row-cols-lg-auto g-3 align-items-center">
        <label for="email" class="form-label">Email:</label>
        <input type="email" id="emailUser" name="emailUser" size="60" maxlength="90" value="<?= $_POST["emailUser"];?>" onclick="this.select();" required>
    </div>

    <div class="row row-cols-lg-auto g-3 align-items-center">
        <label for="confirmemail" class="form-label">Confirm Email:</label>
        <input type="email" id="confirmemail" name="confirmemail" size="60" maxlength="90" value="<?= $_POST["confirmemail"];?>" onclick="this.select();" required>
    </div> 
    
    <div class="row row-cols-lg-auto g-3 align-items-center">
        <label for="descricao" class="form-label">Web3 Polygon(MATIC) Wallet:</label>
        <input type="text" id="web3wallet" name="web3wallet" placeholder="0x..." size="60" maxlength="42" value="<?= $_POST["web3wallet"];?>" onclick="this.select();" required>
    </div>
    
    <div>
        <label for="identificador" class="form-label">Identificador: <?php echo $identificador;?></label>   
    </div>
    <br>
    <p><button id="submitButton" name="submit" class="btn btn-primary">Gerar QR Code <i class="fas fa-qrcode"></i></button></p>
</form>
</body>
<?php include '../en/mobile/price.php';?>
<?php
date_default_timezone_set('UTC');
echo date("H:i:s T d/m/Y") . "<br>";
?>
</html>
