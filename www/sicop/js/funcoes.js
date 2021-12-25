function id( el ){
    return document.getElementById( el );
}

/*
---------------------------------------------------------
Função criada para substituição de palavras para
padronização e evitar campos em brando nos formulários.
para chamar: onBlur="nomedafuncao(this)"
---------------------------------------------------------
*/
function padrao(field){
    var newVal = "N/C";
    if (field.value == "" || field.value == "" || field.value == "" ||
        field.value == "" || field.value == "" || field.value == "" ||
        field.value == "" || field.value == "" || field.value == "" ||
        field.value == "" || field.value == "" || field.value == "" ||
        field.value == "" || field.value == "" || field.value == "" ||
        field.value == "" || field.value == "" || field.value == "" ||
        field.value == "" || field.value == "" || field.value == "") {
        field.value = newVal
    }
}

function rpcnomepai(field, type){
    var newVal = "N/C";

    switch(type){
        case 1: // se o campo ficar em branco nao preenche
            if (field.value == 'NAO CONSTA' || field.value == 'NAO INFORMADO' ||
                field.value == 'NAO SABE' || field.value == 'DESCONHECIDO' || field.value == 'IGNORADO' ||
                field.value == 'NAO DECLARADO' || field.value == 'NAO CONHECEU' || field.value == 'NAO TEM' ||
                field.value == 'NAO POSSUI' || field.value == 'NAO' || field.value == 'N' || field.value == 'NC') {
                field.value = newVal;
            }
            break;
        case 2: // se o campo ficar em branco preenche
            if (field.value == '' || field.value == 'NAO CONSTA' || field.value == 'NAO INFORMADO' ||
                field.value == 'NAO SABE' || field.value == 'DESCONHECIDO' || field.value == 'IGNORADO' ||
                field.value == 'NAO DECLARADO' || field.value == 'NAO CONHECEU' || field.value == 'NAO TEM' ||
                field.value == 'NAO POSSUI' || field.value == 'NAO' || field.value == 'N' || field.value == 'NC') {
                field.value = newVal
            }
            break;
    }
}

function rpcsinais(field){
    var newVal = 'NAO APRESENTA';
    if (field.value == '' || field.value == 'NAO CONSTA' || field.value == 'NAO INFORMADO' ||
        field.value == 'NAO SABE' || field.value == 'DESCONHECIDO' || field.value == 'IGNORADO' ||
        field.value == 'NAO DECLARADO' || field.value == 'NAO CONHECEU' || field.value == 'NAO TEM' ||
        field.value == 'NAO POSSUI' || field.value == 'NAO' || field.value == 'N' || field.value == 'NC' ||
        field.value == 'N/C') {
        field.value = newVal
    }
}

function rpcfuga(field){
    var newVal = 'NADA CONSTA';
    if (field.value == '' || field.value == 'NAO CONSTA' || field.value == 'NAO INFORMADO' ||
        field.value == 'NAO SABE' || field.value == 'DESCONHECIDO' || field.value == 'IGNORADO' ||
        field.value == 'NAO DECLARADO' || field.value == 'NAO CONHECEU' || field.value == 'NAO TEM' ||
        field.value == 'NAO POSSUI' || field.value == 'NAO' || field.value == 'N' || field.value == 'NC') {
        field.value = newVal
    }
}

function rpcvara(field){

    var num = field.value.replace(/\D/g,'');                  //Remove tudo o que não é dígito
    var local = field.value.replace(/[\d\s]/g,'');            //Remove tudo o que é dígito
    var newVal = field.value;
    var newlocal = field.value;

    if (local == 'VU'){
        newlocal = 'VARA ÚNICA';
    }
    if (local == 'VC'){
        newlocal = 'VARA CRIMINAL';
    }
    if (local == 'VF'){
        newlocal = 'VARA FEDERAL';
    }
    if (local == 'VEC'){
        newlocal = 'VARA DAS EXECUÇÕES CRIMINAIS';
    }
    if (local == 'JECRIM'){
        newlocal = 'JUIZADO ESPECIAL CÍVEL E CRIMINAL';
    }
    if (local == 'CP'){
        newlocal = 'CADEIA PÚBLICA';
    }
    if (local == 'DP'){
        newlocal = 'DISTRITO POLICIAL';
    }
    if (local == 'DEPOL'){
        newlocal = 'DELEGACIA DE POLÍCIA';
    }
    if (local == 'DIG'){
        newlocal = 'DELEGACIA DE INVESTIGAÇÕES GERAIS';
    }
    if (local == 'IML'){
        newlocal = 'INSTITUTO MÉDICO LEGAL';
    }
    if (local == 'HB'){
        newlocal = 'AMBULATÓRIO DO HOSPITAL DE BASE';
    }
    if (field.value != newlocal){
        if (num == ''){
            newVal = newlocal;
        } else {
            newVal = num+'ª '+newlocal;
        }
    }
    //alert(num);
    //alert(local);
    field.value = newVal;
}

function preenche_campos_aud(){

    localaud = id('local_aud');
    cidadeaud = id('cidade_aud');


    if (id('tipo_aud_1').checked){
        newlocal = 'AMBULATÓRIO DO HOSPITAL DE BASE ()';
        newcidade = 'SÃO JOSÉ DO RIO PRETO - SP';
    } else if (id('tipo_aud_2').checked){
        newlocal = 'INSTITUTO MÉDICO LEGAL';
        newcidade = 'SÃO JOSÉ DO RIO PRETO - SP';
    } else if (id('tipo_aud_3').checked){
        newlocal = 'EXAME DE DEPENDÊNCIA TOXICOLÓGICA';
        newcidade = 'SÃO JOSÉ DO RIO PRETO - SP';
    } else if (id('tipo_aud_5').checked){
        newlocal = 'PERÍCIA MÉDICA';
        newcidade = 'SÃO JOSÉ DO RIO PRETO - SP';
    } else if (id('tipo_aud_6').checked){
        newlocal = 'NOTIFICADO/CITADO';
        newcidade = 'CATANDUVA - SP';
    } else if (id('tipo_aud_7').checked){ // SEGURO DESEMPREGO
        newlocal = 'CAIXA ECONÔMICA FEDERAL';
        newcidade = 'SÃO JOSÉ DO RIO PRETO - SP';
    } else {
        newlocal = '';
        newcidade = '';
    }

    localaud.value = newlocal;
    cidadeaud.value = newcidade;


}

function altera_campos_aud(){

    if ( id( 'tipo_aud_0' ).checked ){ //JUDICIAL
        id( 'local' ).innerHTML = 'Local:';
        id( 'num' ).innerHTML = 'Nº do processo:';
    } else if ( id( 'tipo_aud_1' ).checked ){ //MÉDICA
        id( 'local' ).innerHTML = 'Local:';
        id( 'num' ).innerHTML = 'Nº do processo:';
    } else if ( id( 'tipo_aud_2' ).checked ){ //IML
        id( 'local' ).innerHTML = 'Local:';
        id( 'num' ).innerHTML = 'Nº do processo:';
    } else if ( id( 'tipo_aud_3' ).checked ){ //EXAME TOXICOLOGICO
        id( 'local' ).innerHTML = 'Para realizar:';
        id( 'num' ).innerHTML = 'Nº do processo:';
    } else if ( id( 'tipo_aud_4' ).checked ){ //DELEGACIA
        id( 'local' ).innerHTML = 'Local:';
        id( 'num' ).innerHTML = 'Nº do inquérito:';
    }else if ( id( 'tipo_aud_5' ).checked ){ //PERICIA INSS
        id( 'local' ).innerHTML = 'Para realizar:';
        id( 'num' ).innerHTML = 'Nº do processo:';
    } else if ( id( 'tipo_aud_6' ).checked ){ //NOTIFICAÇÃO CP
        id( 'local' ).innerHTML = 'A fim de ser:';
        id( 'num' ).innerHTML = 'Nº do processo:';
    } else if ( id( 'tipo_aud_7' ).checked ){ // SEGURO DESEMPREGO
        id( 'local' ).innerHTML = 'Local:';
        id( 'num' ).innerHTML = 'Tipo de atendimento:';
    } else {
        id( 'local' ).innerHTML = 'Local:';
        id( 'num' ).innerHTML = 'Nº do processo:';
    }

}

function oculta_campos_aud(){

    if ( id( 'tipo_aud_1' ).checked ||
         id( 'tipo_aud_2' ).checked ||
         id( 'tipo_aud_5' ).checked ) {

            id( 'num_process_field' ).style.display='none';
            id( 'num_processo' ).value = '';

    } else {

        id( 'num_process_field' ).style.display='';

    }

}

function oculta_motivo_aud( ){

    if ( id( 'sit_aud_0' ).checked ){

        id( 'mot_aud_field' ).style.display='none';
        id( 'motivo_justi' ).value = '';

    } else {

        id( 'mot_aud_field' ).style.display='';

    }

}

function oculta_campos_termo(){

    if ( id( 'mot_termo_0' ).checked ||
         id( 'mot_termo_1' ).checked ){

        id( 'dest' ).style.display='none';
        id( 'destino' ).value = '';

    } else if ( id( 'mot_termo_2' ).checked ) {

        id( 'dest' ).style.display='';

    } else {

        id( 'dest' ).style.display='';

    }

}

function oculta_campos_termo_seg(){

    if ( id( 'mot_termo_0' ).checked ||
         id( 'mot_termo_1' ).checked ){

        id( 'unid_field' ).style.display='none';
        id( 'unid_dest' ).value = '';

    } else {

        id( 'unid_field' ).style.display='';

    }

}

function oculta_campos_termo_rest(){

    if ( id( 'tipo_rest_0' ).checked ){

        id( 'sit_alv' ).style.display='none';

    } else {

        id( 'sit_alv' ).style.display='';

    }

}


function rpccidade( field, minif ){

    var traco = field.value.indexOf('-')
    var cidade = field.value
    var newVal = field.value

    if ( traco == -1 && cidade != '' ){

        if ( cidade == 'RIO PRETO' ||
            cidade == 'SJRPRETO' ||
            cidade == 'SJRIO PRETO' ||
            cidade == 'SJ RIO PRETO' ||
            cidade == 'SJRP' ||
            cidade == 'S J DO RIO PRETO' ||
            cidade == 'SJ DO RIO PRETO' ||
            cidade == 'NESTA' ||
            cidade == 'S J RIO PRETO' ) {

            newVal = 'SÃO JOSÉ DO RIO PRETO - SP';

            if ( minif != undefined && minif != '' ) {
                newVal = 'SJ RIO PRETO - SP';
            }

        } else if ( cidade == 'CAT' || cidade == 'CATA' ) {

            newVal = 'CATANDUVA - SP';

        } else if ( cidade == 'VOT' || cidade == 'VOTU' ) {

            newVal = 'VOTUPORANGA - SP';

        } else if ( cidade == 'BIR' ) {

            newVal = 'BIRIGUI - SP';

        } else if ( cidade == 'ARA'  || cidade == 'ATA' ) {

            newVal = 'ARAÇATUBA - SP';

        } else if ( cidade == 'ANDRA' ) {

            newVal = 'ANDRADINA - SP';

        } else {

            newVal = cidade+' - SP'

        }
        field.value = newVal
    }

}

function rpcartigo(field){

    var art_old = field.value //.replace(/\D/g,'');              //Remove tudo o que não é dígito
    var art_rpc = '';
    var art_new = '';

    if ( art_old.indexOf('10826') != -1 ){
        art_rpc = '10826';
        art_new = '- LEI 10.826/03';
    }
    if ( art_old.indexOf('6368') != -1 ){
        art_rpc = '6368';
        art_new = '- LEI 6.368(ENTORP.)';
    }
    if ( art_old.indexOf('11343') != -1 ){
        art_rpc = '11343';
        art_new = '- LEI 11.343/06';
    }
    if ( art_old.indexOf('11340') != -1 ){
        art_rpc = '11340';
        art_new = '- LEI 11.340/06(MARIA DA PENHA)';
    }
    if ( art_old.indexOf('2252') != -1 ){
        art_rpc = '2252';
        art_new = '- LEI 2.252(CORRUPÇÃO DE MENOR)';
    }
    if ( art_old.indexOf('8069') != -1 ){
        art_rpc = '8069';
        art_new = '- LEI 8.069(ECA)';
    }
    if ( art_old.indexOf('9437') != -1 ){
        art_rpc = '9437';
        art_new = '- LEI 9.437/97(SINARM)';
    }

    if ( art_new != '' ){
        field.value = field.value.replace( art_rpc, art_new );
    }

}


// ----------------------------------------------
// Função que pega o ultimo numero de uma
// sequencia. Usada para as funções de
// verificação de digito.
// ----------------------------------------------
function dig(field){
    var comp = field.value.length;
    var ult = field.value.charAt(comp - 1);
    return ult;
}

function digcpf(field){

    //return field.value.substr(-2, 2);
    var comp = field.value.length;
    return field.value.substr(comp-2, 2);
}

// ----------------------------------------------
// Calcula o dígito em Módulo 10 do número dado.
// Os valores de entrada e saída são string.
// ----------------------------------------------
function caldmatr(matr){

    matr = matr.replace(/[.-]/g,''); //retira pontos e traço do número

    var i;
    var mult = 2;
    var soma = 0;
    var s = '';

    for ( i = matr.length - 1; i >= 0; i-- ){
        s = (mult * parseInt(matr.charAt(i))) + s;
        if (--mult<1){
            mult = 2;
        }
    }

    for (i=0; i<s.length; i++){
        soma = soma + parseInt(s.charAt(i));
    }

    soma = soma % 10;

    if (soma != 0){
        soma = 10 - soma;
    }

    //alert(soma);
    return soma.toString();

}

function caldcpf1(cpf){

    cpf = trim(cpf);

    cpf = cpf.replace('.','');
    cpf = cpf.replace('.','');
    cpf = cpf.replace('-','');

    while(cpf.length < 11) cpf = "0"+ cpf;
    var expReg = /^0+$|^1+$|^2+$|^3+$|^4+$|^5+$|^6+$|^7+$|^8+$|^9+$/;
    var a = [];
    var b = new Number;
    var c = 11;

    for (i=0; i<11; i++){
        a[i] = cpf.charAt(i);
        if (i < 9) b += (a[i] * --c);
    }

    if ( (x = b % 11) < 2 ) {
        a[9] = 0
    } else {
        a[9] = 11-x
    }

    b = 0;
    c = 11;

    for (y=0; y<10; y++) b += (a[y] * c--);
    if ((x = b % 11) < 2) {
        a[10] = 0;
    } else {
        a[10] = 11-x;
    }

    if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10]) || cpf.match(expReg)) return false;

    return true;

}

function caldcpf( cpf ) {

    erro = new String;

    //cpf = trim(cpf);
    cpf = cpf.replace(/[.-]/g,''); //retira pontos e traço do número

    //if (cpf.length < 11) erro += "Sao necessarios 11 digitos para verificacao do CPF! \n\n";

//    var nonNumbers = /\D/;
//
//    if (nonNumbers.test(cpf)) erro += "A verificacao de CPF suporta apenas numeros! \n\n";

    if ( cpf == "00000000000" ||
         cpf == "11111111111" ||
         cpf == "22222222222" ||
         cpf == "33333333333" ||
         cpf == "44444444444" ||
         cpf == "55555555555" ||
         cpf == "66666666666" ||
         cpf == "77777777777" ||
         cpf == "88888888888" ||
         cpf == "99999999999"){
        erro += "Numero de CPF invalido!"
    }

    var a = [];
    var b = new Number;
    var c = 11;

    for (i=0; i<11; i++){
        a[i] = cpf.charAt(i);
        if (i < 9) b += (a[i] * --c);
    }

    if ((x = b % 11) < 2) {
        a[9] = 0
    } else {
        a[9] = 11-x
    }

    b = 0;
    c = 11;

    for (y=0; y<10; y++) b += (a[y] * c--);

    if ((x = b % 11) < 2) {
        a[10] = 0;
    } else {
        a[10] = 11-x;
    }

    var digcpf = a[9].toString() + a[10].toString();

    //alert( 'FUNCAO ' + digcpf );

    return digcpf.toString();

//    alert(digcpf);
//    return false;

//    if ((cpf.charAt(9) != a[9]) || (cpf.charAt(10) != a[10])){
//        erro +="Digito verificador com problema!";
//    }
//
//    if (erro.length > 0){
//        alert(erro);
//        return false;
//    }
//
//    return true;

}


/*
---------------------------------------------------------
  função que efetua o calculo do digito verificador
  tendo como parametro o rg sem o digito
  retira pontos e traço do rg
  é chamada por outras funções como a checkrg()
---------------------------------------------------------
*/
function caldrg(rg){

    if (rg == '') {
        return '';
    }

    rg = rg.replace(/[.-]/g,''); //retira pontos e traço do número

    var comp = rg.length; //pega o comprimento do rg

    //pega cada número do rg individualmente
    var d1 = (rg.substr(comp - 8,1));
    var d2 = (rg.substr(comp - 7,1));
    var d3 = (rg.substr(comp - 6,1));
    var d4 = (rg.substr(comp - 5,1));
    var d5 = (rg.substr(comp - 4,1));
    var d6 = (rg.substr(comp - 3,1));
    var d7 = (rg.substr(comp - 2,1));
    var d8 = (rg.substr(comp - 1,1));

    comp - 8 < 0 ? d1 = 0 : d1 = d1 //caso o rg tenha menos de 8 números, o d1 fica valendo 0 (zero)
    comp - 7 < 0 ? d2 = 0 : d2 = d2 //caso o rg tenha menos de 7 números, o d2 fica valendo 0 (zero)

    //faz as multiplicações de cada número
    var m1 = d1 * 2;
    var m2 = d2 * 3;
    var m3 = d3 * 4;
    var m4 = d4 * 5;
    var m5 = d5 * 6;
    var m6 = d6 * 7;
    var m7 = d7 * 8;
    var m8 = d8 * 9;

    var total = (m1 + m2 + m3 + m4 + m5 + m6 + m7 + m8) * 10;

    var dv = total % 11; //pega o resto da divisão por 11

    if (dv == 10) {
        dvcalc = 'X';
    } else {
        dvcalc = dv;
    }
    //alert(comp+' - '+dvcalc);
    return dvcalc;
}

/*
---------------------------------------------------------
  função para checar se o rg foi digitado corretamente
  comparando o rg com o digito verificador
  retira pontos e traço do rg
  para chamar: onBlur='checkrg(this, this.value);'
---------------------------------------------------------
*/
function checkrg(campo, valorrg){

    if (valorrg == '') {
        return '';
    }

    valorrg = valorrg.replace(/[.-]/g,''); //retira pontos e traço do número

    var comp = valorrg.length

    if (comp < 2) {
        alert('Valor incorreto!');
        campo.focus();
        return '';
    }

    valorrg = valorrg.substring(0, comp - 1);

    var digito = caldrg(valorrg);

    var d = dig(campo);

    if (digito != d){
        alert('O R.G. digitado não é compatível com o dígito. Verifique!')
        campo.focus();
        return false;
    } else {
        return true;
    }
}

/*
---------------------------------------------------------
  função para checar se a matrícula foi digitada
  corretamente comparando a matrícula com o digito
  verificador. Retira pontos e traço da matrícula
  para chamar: onBlur='checkmatr(this, this.value);'
---------------------------------------------------------
*/
function checkmatr(campo, valormatr){

    if (valormatr == '') { //se nao tiver valor retorna nada
        return '';
    }

    valormatr = valormatr.replace(/[.-]/g,''); //retira pontos e traço do número

    var comp = valormatr.length //pega o comprimento

    if (comp < 2) {
        alert('Valor incorreto!');
        campo.value = '';
        campo.focus();
        return false;
    }

    valormatr = valormatr.substring(0, comp - 1);

    var digito = caldmatr(valormatr);

    var d = dig(campo);

    if (digito != d){
        alert('A matrícula digitada não é compatível com o dígito. Verifique!')
        campo.focus();
        return false;
    }else{
        return true;
    }
}

/*
---------------------------------------------------------
  função para checar se a matrícula foi digitada
  corretamente comparando a matrícula com o digito
  verificador. Retira pontos e traço da matrícula
  para chamar: onBlur='checkmatr(this, this.value);'
---------------------------------------------------------
*/
function checkcpf(campo, valorcpf){

    if (valorcpf == '') { //se nao tiver valor retorna nada
        return '';
    }

    valorcpf = valorcpf.replace(/[.-]/g,''); //retira pontos e traço do número

    var comp = valorcpf.length //pega o comprimento

    if (comp < 11) {
        alert('Valor incorreto!');
        campo.value = '';
        campo.focus();
        return false;
    }

    if ( valorcpf == "00000000000" ||
         valorcpf == "11111111111" ||
         valorcpf == "22222222222" ||
         valorcpf == "33333333333" ||
         valorcpf == "44444444444" ||
         valorcpf == "55555555555" ||
         valorcpf == "66666666666" ||
         valorcpf == "77777777777" ||
         valorcpf == "88888888888" ||
         valorcpf == "99999999999"){
        alert('Numero de CPF invalido!');
        campo.value = '';
        campo.focus();
        return false;
    }

    valorcpf = valorcpf.substring(0, comp - 2);

    var digito = caldcpf(valorcpf);



    var d = digcpf(campo);

    //alert(d);

    if (digito != d){
        alert('O CPF digitado não é compatível com os dígitos verificadores. Verifique!')
        campo.focus();
        return false;
    }else{
        return true;
    }
}

// adicionado em 05/12/2009

// version: beta
// created: 2005-08-30
// updated: 2005-08-31
// mredkj.com

/*
---------------------------------------------------------
Função que bloqueia a digitação de caracteres de acordo
com o tipo
para chamar: onKeyPress='return SoNumeros(event, oNumeroDoTipo);'
---------------------------------------------------------
*/

function blockChars(e, type) {
    var key;
    var keychar;
    var reg;

    if(window.event) {
        // for IE, e.keyCode or window.event.keyCode can be used
        key = e.keyCode;
    } else if(e.which) {
        // netscape
        key = e.which;
    } else {
        // no event, so pass through
        return true;
    }

    switch(type){
        case 1: // permite só letras

            keychar = String.fromCharCode(key);

            if (key == 8 || key == 13 || key == 32 ) return true; // 8 = backspace, 13 = enter, 32 = espaço

            reg = /[A-ZÇÁÀÂÃÉÈÊÍÌÎÓÒÔÕÚÙÛa-zçáàâãéèêíìîóòôõúùû]/;

            return reg.test(keychar);

            break;
        case 2: // permite só numeros

            if (key == 8 || key == 13) return true;

            keychar = String.fromCharCode(key);

            reg = /[0-9]/;

            return reg.test(keychar);

            break;
        case 3: // permite letras e numeros

            keychar = String.fromCharCode(key);

            if (key == 8 || key == 13 || key == 32 ) return true; // 8 = backspace, 13 = enter, 32 = espaço

            reg = /[A-ZÇÁÀÂÃÉÈÊÍÌÎÓÒÔÕÚÙÛa-zçáàâãéèêíìîóòôõúùû0-9]/;

            return reg.test(keychar);

            break;
        case 4: // permite letras, números e alguns caracteres especiais

            keychar = String.fromCharCode(key);

            if (key == 8 || key == 13 || key == 32 ) return true; // 8 = backspace, 13 = enter, 32 = espaço

            reg = /[A-ZÇÁÀÂÃÉÈÊÍÌÎÓÒÔÕÚÙÛa-zçáàâãéèêíìîóòôõúùû0-9(),\?\!\:\.\/\-\§\ª\º\+\>\<\=]/;

            return reg.test(keychar);

            break;
        case 5: // permite numeros e pontos e traços

            if (key == 8 || key == 13 || key == 32) return true;

            keychar = String.fromCharCode(key);

            reg = /[0-9\.\-]/;

            return reg.test(keychar);

            break;
        case 6: // permite números e alguns caracteres especiais

            keychar = String.fromCharCode(key);

            if (key == 8 || key == 13 || key == 32 ) return true; // 8 = backspace, 13 = enter, 32 = espaço

            reg = /[0-9(),\?\!\.\/\-\§\ª\º]/;

            return reg.test(keychar);

            break;
        case 7: // permite numeros, pontos, traços e o 'x' para RGs
            if (key == 8 || key == 13 || key == 32) return true;

            keychar = String.fromCharCode(key);

            reg = /[Xx0-9\.\-]/;

            return reg.test(keychar);

            break;

    }

}


//Remove Letter Accents from Text (Javascript) is Copyright MyTextTools.com
//Webmasters: When borrowing any MyTextTools.com tool scripting always include a LinkBack saying 'Tool courtesy of: MyTextTools.com' without nofollow.

String.prototype.accnt = function(){
    var cnt = 0;
    var acnt = this;

    acnt = acnt.split('');
    acntlen = acnt.length;

    var sec = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÕÖØòóôõöøÈÉÊËèéêëðÇçÐÌÍÎÏìíîïÙÚÛÜùúûüÑñŠšŸÿýŽž';
    var rep = ['A','A','A','A','A','A','a','a','a','a','a','a','O','O','O','O','O','O','O','o','o','o','o','o','o','E','E','E','E','e','e','e','e','e','C','c','D','I','I','I','I','i','i','i','i','U','U','U','U','u','u','u','u','N','n','S','s','Y','y','y','Z','z'];

    for (var y = 0; y < acntlen; y++){
        if (sec.indexOf(acnt[y]) != -1)  cnt++;
    }
    return cnt;
}

String.prototype.renlacc = function(){

    var torem = this;
    torem = torem.split('');
    toremout = new Array();
    toremlen = torem.length;
    var sec = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÕÖØòóôõöøÈÉÊËèéêëðÇçÐÌÍÎÏìíîïÙÚÛÜùúûüÑñŠšŸÿýŽž';
    var rep = ['A','A','A','A','A','A','a','a','a','a','a','a','O','O','O','O','O','O','O','o','o','o','o','o','o','E','E','E','E','e','e','e','e','e','C','c','D','I','I','I','I','i','i','i','i','U','U','U','U','u','u','u','u','N','n','S','s','Y','y','y','Z','z'];
    for (var y = 0; y < toremlen; y++){
        if (sec.indexOf(torem[y]) != -1) {
            toremout[y] = rep[sec.indexOf(torem[y])];
        } else toremout[y] = torem[y];
    }
    toascout = toremout.join('');
    return toascout;
}

/*
---------------------------------------------------------
Função que remove acentos.
para chamar: onBlur='remacc(this);'
---------------------------------------------------------
*/
function remacc(src){

    var field = src;
    var countarr = new Array();
    var c = '';
    var text=field.value;
    var textout = new Array();

    text = text.replace(/\r/g,'');
    text = text.split('\n');

    var linecnt = text.length;

    for (var x = 0; x < linecnt; x++){
        countarr[x] = Math.abs(text[x].accnt());
        textout[x] = text[x].renlacc();
    }
    textout = textout.join('\n');
    field.value=textout;
    var countout = 0
}

/*
---------------------------------------------------------
Função que transforma todas as letras do campo
em MAIUSCULAS.
para chamar: onBlur='upperMe(this);'
---------------------------------------------------------
*/
function upperMe(field) {
    var upperCaseVersion = field.value.toUpperCase();
    field.value = upperCaseVersion;
}

/*
---------------------------------------------------------
Função que transforma todas as letras do campo
em minusculas.
para chamar: onBlur='lowerMe(this);'
---------------------------------------------------------
*/
function lowerMe(field) {
    var upperCaseVersion = field.value.toLowerCase();
    field.value = upperCaseVersion;
}

function extractNumber(obj, decimalPlaces, allowNegative) {
    var temp = obj.value;

    // avoid changing things if already formatted correctly
    var reg0Str = '[0-9]*';
    if (decimalPlaces > 0) {
        reg0Str += '\\.?[0-9]{0,' + decimalPlaces + '}';
    } else if (decimalPlaces < 0) {
        reg0Str += '\\.?[0-9]*';
    }
    reg0Str = allowNegative ? '^-?' + reg0Str : '^' + reg0Str;
    reg0Str = reg0Str + '$';
    var reg0 = new RegExp(reg0Str);
    if (reg0.test(temp)) return true;

    // first replace all non numbers
    var reg1Str = '[^0-9' + (decimalPlaces != 0 ? '.' : '') + (allowNegative ? '-' : '') + ']';
    var reg1 = new RegExp(reg1Str, 'g');
    temp = temp.replace(reg1, '');

    if (allowNegative) {
        // replace extra negative
        var hasNegative = temp.length > 0 && temp.charAt(0) == '-';
        var reg2 = /-/g;
        temp = temp.replace(reg2, '');
        if (hasNegative) temp = '-' + temp;
    }

    if (decimalPlaces != 0) {
        var reg3 = /\./g;
        var reg3Array = reg3.exec(temp);
        if (reg3Array != null) {
            // keep only first occurrence of .
            //  and the number of places specified by decimalPlaces or the entire string if decimalPlaces < 0
            var reg3Right = temp.substring(reg3Array.index + reg3Array[0].length);
            reg3Right = reg3Right.replace(reg3, '');
            reg3Right = decimalPlaces > 0 ? reg3Right.substring(0, decimalPlaces) : reg3Right;
            temp = temp.substring(0,reg3Array.index) + '.' + reg3Right;
        }
    }

    obj.value = temp;
}

function blockNonNumbers(obj, e, allowDecimal, allowNegative)
{
    var key;
    var isCtrl = false;
    var keychar;
    var reg;

    if(window.event) {
        key = e.keyCode;
        isCtrl = window.event.ctrlKey
    }
    else if(e.which) {
        key = e.which;
        isCtrl = e.ctrlKey;
    }

    if (isNaN(key)) return true;

    keychar = String.fromCharCode(key);

    // check for backspace or delete, or if Ctrl was pressed
    if (key == 8 || isCtrl)
    {
        return true;
    }

    reg = /\d/;
    var isFirstN = allowNegative ? keychar == '-' && obj.value.indexOf('-') == -1 : false;
    var isFirstD = allowDecimal ? keychar == '.' && obj.value.indexOf('.') == -1 : false;

    return isFirstN || isFirstD || reg.test(keychar);
}

/* Máscaras ER */

/*
e para chamar elas, é:

CEP: <input type='text' name='cep' onkeypress='mascara(this, mcep)' size='10' maxlength='9' value='' />

topico do imasters: http://forum.imasters.uol.com.br/index.php?/topic/357459-mascara/page__p__1363848&#entry1363848
*/
function mascara(o,f){
    v_obj=o
    v_fun=f
    setTimeout("execmascara()",1)
}
function execmascara(){
    v_obj.value=v_fun(v_obj.value)
}
function mcep(v){
    v=v.replace(/\D/g,'')                    //Remove tudo o que não é dígito
    v=v.replace(/^(\d{5})(\d)/,"$1-$2")      //Esse é tão fácil que não merece explicações
    return v
}
function mtel(v){
    v=v.replace(/\D/g,"")                 //Remove tudo o que não é dígito
    v=v.replace(/^(\d\d)(\d)/g,"($1) $2") //Coloca parênteses em volta dos dois primeiros dígitos
    v=v.replace(/(\d{4})(\d)/,"$1-$2")    //Coloca hífen entre o quarto e o quinto dígitos
    return v
}
function cnpj(v){
    v=v.replace(/\D/g,"")                           //Remove tudo o que não é dígito
    v=v.replace(/^(\d{2})(\d)/,"$1.$2")             //Coloca ponto entre o segundo e o terceiro dígitos
    v=v.replace(/^(\d{2})\.(\d{3})(\d)/,"$1.$2.$3") //Coloca ponto entre o quinto e o sexto dígitos
    v=v.replace(/\.(\d{3})(\d)/,".$1/$2")           //Coloca uma barra entre o oitavo e o nono dígitos
    v=v.replace(/(\d{4})(\d)/,"$1-$2")              //Coloca um hífen depois do bloco de quatro dígitos
    return v
}
function mcpf(v){
    v=v.replace(/\D/g,"")                    //Remove tudo o que não é dígito
    v=v.replace(/(\d{3})(\d)/,"$1.$2")       //Coloca um ponto entre o terceiro e o quarto dígitos
    v=v.replace(/(\d{3})(\d)/,"$1.$2")       //Coloca um ponto entre o terceiro e o quarto dígitos
    //de novo (para o segundo bloco de números)
    v=v.replace(/(\d{3})(\d{1,2})$/,"$1-$2") //Coloca um hífen entre o terceiro e o quarto dígitos
    return v
}
function mdata(v){
    v=v.replace(/\D/g,"");                    //Remove tudo o que não é dígito
    v=v.replace(/(\d{2})(\d)/,"$1/$2");
    v=v.replace(/(\d{2})(\d)/,"$1/$2");

    v=v.replace(/(\d{2})(\d{2})$/,"$1$2");
    return v;
}
function mtempo(v){
    v=v.replace(/\D/g,"");                    //Remove tudo o que não é dígito
    v=v.replace(/(\d{1})(\d{2})(\d{2})/,"$1:$2.$3");
    return v;
}
function mhora(v){
    v=v.replace(/\D/g,"");                    //Remove tudo o que não é dígito
    v=v.replace(/(\d{2})(\d)/,"$1h$2");
    return v;
}
function mrg1(v){
    v=v.replace(/\D/g,'');                  //Remove tudo o que não é dígito
    v=v.replace(/^([0]){1}/g, '')
    v=v.replace(/(\d)(\d{7})$/,"$1.$2");    //Coloca o . antes dos últimos 3 dígitos, e antes do verificador
    v=v.replace(/(\d)(\d{4})$/,"$1.$2");    //Coloca o . antes dos últimos 3 dígitos, e antes do verificador
    v=v.replace(/(\d)([0-9x])$/,"$1-$2");       //Coloca o - antes do último dígito
    return v;
}
function mrg(v){
    v=v.replace(/[^0-9x]/gi,'');                    //Remove tudo o que não é dígito
    v=v.replace(/^([0x]){1}/gi, '');                //impede a digitação de 0 e x no começo
    v=v.replace(/([0-9x])([0-9x]{7})$/i,"$1.$2");    //Coloca o . antes dos últimos 3 dígitos, e antes do verificador
    v=v.replace(/([0-9x])([0-9x]{4})$/i,"$1.$2");    //Coloca o . antes dos últimos 3 dígitos, e antes do verificador
    v=v.replace(/([0-9x])([0-9x])$/i,"$1-$2");       //Coloca o - antes do último dígito
    v=v.replace(/([x])/gi, 'X');                                //substitui o x minusculo pelo maiusculo
    v=v.replace(/([x]){2,}/gi, '');                                //2 ou mais ocorrencias de x retorna ''
    v=v.replace(/([0-9][x]|[x][0-9]|[x]\-|^\-[x]){1,}/gi, '');    //x antecedido ou procedido de numero, antes do traço, ou depois no começo da cadeia
    return v;
}

function mmatr(v){
    v=v.replace(/\D/g,"");                  //Remove tudo o que não é dígito
    v=v.replace(/^([0]){1}/g, '');
    v=v.replace(/(\d)(\d{7})$/,"$1.$2");    //Coloca o . antes dos últimos 3 dígitos, e antes do verificador
    v=v.replace(/(\d)(\d{4})$/,"$1.$2");    //Coloca o . antes dos últimos 3 dígitos, e antes do verificador
    v=v.replace(/(\d)(\d)$/,"$1-$2");       //Coloca o - antes do último dígito
    return v;
}

function mmonet(v){
    v=v.replace(/\D/g,"");                  //Remove tudo o que não é dígito
    //v=v.replace(/^([0]){1}/g, '');
    v=v.replace(/(\d)(\d{8})$/,"$1.$2");    //Coloca o . antes dos últimos 3 dígitos, e antes do verificador
    v=v.replace(/(\d)(\d{5})$/,"$1.$2");    //Coloca o . antes dos últimos 3 dígitos, e antes do verificador
    v=v.replace(/(\d)(\d{2})$/,"$1,$2");       //Coloca o - antes do último dígito
    return v;
}

function mexec(v){
    v=v.replace(/\D/g,"");                  //Remove tudo o que não é dígito
    v=v.replace(/^([0]){1}/g, '');
    v=v.replace(/(\d)(\d{6})$/,"$1.$2");    //Coloca o . antes dos últimos 3 dígitos, e antes do verificador
    v=v.replace(/(\d)(\d{3})$/,"$1.$2");    //Coloca o . antes dos últimos 3 dígitos, e antes do verificador
    //v=v.replace(/(\d)(\d)$/,"$1-$2");     //Coloca o - antes do último dígito
    return v;
}
function mest(v){
    v=v.replace(/\D/g,"")                    //Remove tudo o que não é dígito
    v=v.replace(/^(\d{1})(\d)/,"$1,$2")      //Esse é tão fácil que não merece explicações
    return v
}
function mnum(v){
    v=v.replace(/\D/g,"");                  //Remove tudo o que não é dígito
    return v;
}

function textCounter(field, maxlimit) {
    if (field.value.length > maxlimit)
        field.value = field.value.substring(0, maxlimit);
}



// Mostra ajuda
//ex.: onFocus=SetHelp('Informe o valor com os centavos.')
// coloque isso no campo que quer que dispare o help
function SetHelp( txt ) {
    id( 'h_status' ).innerHTML = txt;
}


//Formata valor
//ex.: onKeyDown='FormataValor('valor', 13, event)
//Obs.: 'valor' é o nome do campo, 13 o tamanho máximo permitido de carac. do campo e event é a tecla pressionada'
function FormataValor(obj,tammax,teclapres) {
 var tecla = teclapres.keyCode;
 vr = obj.value;
 vr = vr.replace( '/', '' );
 vr = vr.replace( '/', '' );
 vr = vr.replace( ',', '' );
 vr = vr.replace( ',', '' );
 vr = vr.replace( '.', '' );
 vr = vr.replace( '.', '' );
 vr = vr.replace( '.', '' );
 vr = vr.replace( '.', '' );
 //Replaces adicionais
 //vr = vr.replace( '-', '' );
 //vr = vr.replace( '+', '' );
 //vr = vr.replace( '*', '' );
 tam = vr.length;

 if (tam < tammax && tecla != 8){tam = vr.length + 1 ;}

 if (tecla == 8 ){tam = tam - 1 ;}

 if ( tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105 ){
  if ( tam <= 2 ){
   obj.value = vr ;}
  if ( (tam > 2) && (tam <= 5) ){
   obj.value = vr.substr( 0, tam - 2 ) + ',' + vr.substr( tam - 2, tam ) ;}
  if ( (tam >= 6) && (tam <= 8) ){
   obj.value = vr.substr( 0, tam - 5 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ;}
  if ( (tam >= 9) && (tam <= 11) ){
   obj.value = vr.substr( 0, tam - 8 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ;}
  if ( (tam >= 12) && (tam <= 14) ){
   obj.value = vr.substr( 0, tam - 11 ) + '.' + vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ;}
  if ( (tam >= 15) && (tam <= 17) ){
   obj.value = vr.substr( 0, tam - 14 ) + '.' + vr.substr( tam - 14, 3 ) + '.' + vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ;}
 }
}



/* FUNÇÕES DE DATA E HORA*/
function mascara_data(field, data){

    var mydata = '';
    mydata = mydata + data;
    if (mydata.length == 2){
        mydata = mydata + '/';
        field.value = mydata;
    }
    if (mydata.length == 5){
        mydata = mydata + '/';
        field.value = mydata;
    }
}

function verifica_data( field, data ) {

    if ( field.value == '' ) {
        return true;
    }

    var dia = ( field.value.substring(0,2) );
    var mes = ( field.value.substring(3,5) );
    var ano = ( field.value.substring(6,10) );

    var situacao = true;

    if ( field.value != '' ){

        if ( ano < 31 ){

            ano = '20' + ano;
            field.value = dia + '/' + mes + '/' + ano;

        } else {

            if ( ( ano > 30 ) && ( ano < 100 ) ) {
                ano = '19' + ano;
                field.value = dia + '/' + mes + '/' + ano;
            }

        }

    }

    // verifica o dia valido para cada mes
    if ( situacao ) {
        if ( ( dia < 01 ) || ( dia < 01 || dia > 30 ) && (  mes == 04 || mes == 06 || mes == '09' || mes == 11 ) || dia > 31 ) {
            situacao = false;
        }
    }
    // verifica se o mes e valido
    if ( situacao ) {
        if ( mes < 01 || mes > 12 ) {
            situacao = false;
        }
    }

    // verifica se e ano bissexto
    if ( situacao ) {
        if ( mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && ( parseInt(ano / 4 ) != ano / 4 ) ) ) ) {
            situacao = false;
        }
    }

    // verifica se o ano esta entre a faixa estipulada
    if ( situacao ) {
        if ( ano < 1910 || ano > 2030 ) {
            situacao = false;
        }
    }

    if ( !situacao ) {
        alert( 'Data inválida!' );
        field.focus();
        field.value = '';
        situacao = false;
    }

    return situacao;

}

function mascara_hora( field, hora ){
    var myhora = '';
    myhora = myhora + hora;
    if (myhora.length == 2){
        myhora = myhora + ':';
        field.value = myhora;
    }
}

function verifica_hora( field, hora ){

    hrs = (field.value.substring(0,2));
    minu = (field.value.substring(3,5));

    situacao = '';
    // verifica data e hora
    if ((hrs < 00 ) || (hrs > 23) || ( minu < 00) ||( minu > 59)){
        situacao = 'falsa';
    }

    if ( minu.length < 2){
        situacao = 'falsa';
    }

    if (field.value == '') {
        situacao = '';
    }

    if ( situacao == 'falsa' ) {
        alert( 'Hora inválida!' );
        field.focus();
    }
}
/* FIM DAS FUNÇÕES DE DATA E HORA*/

function mostraFuga() {
    if ( id('fuga_0').checked ) {
        id('localfugal').style.visibility='visible';
        id('localfuga').style.visibility='visible';
    } else if (id('fuga_1').checked) {
        id('localfugal').style.visibility='hidden';
        id('localfuga').style.visibility='hidden';
        id('local_fuga').value = ''
    } else {
        id('localfugal').style.visibility='hidden';
        id('localfuga').style.visibility='hidden';
    }
}

function mostraDest() {
    if (id('tipo_mov').value == '4' || id('tipo_mov').value == '8') {
        id('localmov_field').style.display='none';
        //id('localmovl').style.visibility='hidden';
        //id('localmov').style.visibility='hidden';
        id('local_mov').value = '';
    } else {
        id('localmov_field').style.display='';
        //id('localmovl').style.visibility='visible';
        //id('localmov').style.visibility='visible';
    }
}

function mostraPDA() {
    if (id('sit_pda_1').checked) {
        id('tr_sit_det').style.display='';
        id('tr_dt_reab').style.display='';
    } else {
        id('tr_sit_det').style.display='none';
        id('tr_dt_reab').style.display='none';
        id('situacaodet').value = ''
        id('data_reabilit').value = ''
    }
}

function mostraPERT() {
    if (id('retirado').checked) {
        id('tr_pert_ret').style.display='';
    } else {
        id('tr_pert_ret').style.display='none';
        id('obs_ret').value = ''
    }
}

function mostra_susp_visit() {
    if (id('tipo_susp_0').checked) {
        id('tr_dias_susp').style.display='';
    } else {
        id('tr_dias_susp').style.display='none';
        id('periodo').value = ''
    }
}

function mostraPDA_APCC() {
    if (id('conduta').value != '4') {
        id('f_pda').style.visibility='hidden';
        id('pda').value='';
    } else {
        id('f_pda').style.visibility='visible';
    }
}

function datahoje(campo){
    id(campo).value = id('datahj').value //day+'/'+month+'/'+year;
}

function mudar_cor_over(linha){
    linha.style.backgroundColor='#CCCCCC'//'#66ff33'
}
function mudar_cor_out(linha){
    linha.style.backgroundColor=''
}

function ow ( URL, w, h, targ ){

    var winl = (screen.width - w) / 2;
    var wint = (screen.height - h) / 2;

    if ( targ == undefined || targ == '' ) {
        targ = '';
    }

    window.open( URL, targ,'width='+w+',height='+h+',top='+wint+',left='+winl+',toolbar=no, location=no, directories=no, menubar=no, scrollbars=0, resizable=0, status=0' );

}

/*
 * Altera o form para ser submitado em uma nova janela.
 * não cria o form na página, o form já deve existir.
 * @param form string a id do form
 * @param new_link string o caminho do action
 * @return true sempre
 */
function submit_form_nw( form, new_link ) {

    // pega o form pelo id passado
    var formulario = id( form );

    // largura da janela que será aberta
    //var w = '600';

    // altura da janela que será aberta
    //var h = '600';

    // calculos para encontra o meio da tela para colocar a janela
    //var winl = ( screen.width - w ) / 2;
    //var wint = ( screen.height - h ) / 2;

    // abre a nova janela
    //window.open( '', 'new_win', 'width='+w+', height='+h+', top='+wint+', left='+winl+', toolbar=no, location=no, directories=no, menubar=no, scrollbars=0, resizable=0, status=0' );

    // abre uma nova janela com um target definido
    ow( '', '600', '600', 'new_win' );

    // altera o action (destino) do form
    formulario.action = new_link;

    // coloca o target no form igual ao da janela que foi aberta
    formulario.target = 'new_win';

    // submita o form
    formulario.submit();

    // reseta o action e o target do form
    formulario.action = '';
    formulario.target = '';

    // retorna true
    return true;

}

/*
 * Altera o form para ser submitado passando o link do action.
 * abre na mesma janela
 * não cria o form na página, o form já deve existir.
 * @param form string a id do form
 * @param link string o caminho do action
 */
function submit_form_nlk ( form, link ) {

    // pega o form pelo id passado
    var formulario = id( form );

    // altera o action (destino) do form
    formulario.action = link;

    // reseta o target do form
    formulario.target = '';

    // submita o form
    formulario.submit();

    // reseta o action do form
    formulario.action = '';

}

/*
 * Altera o form para ser submitado passando o link do action.
 * (nwid) = new window com id = nova janela com id ( campo de parametro ).
 * monta o form na página e abre em nova janela.
 * @param form_action string o action do form
 * @param field_name string o nome do campo que será passado de parametro
 * @param field_value string o valor do campo de parametro
 */
function submit_form_nwid( form_action, field_name, field_value ){

    // monta o form com os parametros da função
    var form = '';
    form += "<form action='"+form_action+"' method='post' id='nwid' name='nwid'  target='new_win'>";
    form += "<input type='hidden' name='"+field_name+"' value="+field_value+" />";
    form += "</form>";

    // manda para a tag no documento
    saida.innerHTML = form;

    // abre uma nova janela com um target definido
    ow( '', '600', '600', 'new_win' );

    // submita o form
    id( 'nwid' ).submit();

    // reseta a tag do documento
    saida.innerHTML = '';

}

/*
 * Altera o form para ser submitado passando o link do action.
 * (id) = com id = com id ( campo de parametro ).
 * monta o form na página e abre na mesma janela.
 * @param form_action string o action do form
 * @param field_name string o nome do campo que será passado de parametro
 * @param field_value string o valor do campo de parametro
 * @param proced_name string o nome do campo em que será passado o prodedimento
 * @param proced_value string o valor do campo em que será passado o prodedimento
 */
function submit_form_id( form_action, field_name, field_value, proced_name, proced_value ){

    if ( form_action == '' ) {
        return false;
    }

    if ( field_name == '' ) {
        return false;
    }

    if ( field_value == '' || isNaN( field_value ) ) {
        field_value = 1;
    }

    if ( proced_name == undefined ) {
        proced_name = '';
    }

    if ( proced_name != '' ) {

        if ( proced_value == '' || isNaN( proced_value ) ) {
            proced_value = 1;
        }

    }

    // monta o form com os parametros da função
    var form = '';
    form += "<form action='"+form_action+"' method='post' id='js_form' name='js_form'>";
    form += "<input type='hidden' name='"+field_name+"' value="+field_value+" />";
    if ( proced_name != '' ) {
        form += "<input type='hidden' name='"+proced_name+"' value="+proced_value+" />";
    }
    form += "</form>";

    // manda para a tag no documento
    saida.innerHTML = form;

    // submita o form
    id( 'js_form' ).submit();

    // reseta a tag do documento
    saida.innerHTML = '';

    return true;

}

function submit_form ( form, link, proced, sub_proced ) {

    // pega o form pelo id passado
    var formulario = id( form );

    // altera o action (destino) do form
    formulario.action = link;

    // reseta o target do form
    formulario.target = '';

    if ( proced != undefined ) {
        id( 'proced' ).value = proced;
    }

    if ( sub_proced != undefined ) {
        id( 'sub_proced' ).value = sub_proced;
    }

    // submita o form
    formulario.submit();

    // reseta o action e o target do form
    formulario.action = '';
    formulario.target = '';
    if ( proced != undefined ) {
        id( 'proced' ).value = '';
    }
    if ( sub_proced != undefined ) {
        id( 'sub_proced' ).value = ''
    }

}

function submit_form_ ( form ) {
    id( form ).submit();
}

function marca_todos( valor, idtab ){
    var inputs = id( idtab ).getElementsByTagName('input');
    for( var i=0; i<inputs.length; i++ ){
        if( inputs[i].type=='radio' ){
            if( inputs[i].value == valor ){
                inputs[i].checked='checked';
            }
        }
    }
}
function pega_radio( idtr, idtab ){
    var inputs = id( idtr ).getElementsByTagName('input');
    for( var i=0; i<inputs.length; i++ ){
        if( inputs[i].type=='radio' ){
            inputs[i].onclick = function(){
                marca_todos( this.value, idtab );
            }
        }
    }
}

function selectAll(checkAll, nameChecks){
    var check = document.getElementsByName(checkAll)[0];
    var checks = document.getElementsByName(nameChecks);
    var checkeds = 0;

    check.onclick = checksAll;

    function checksAll(){
        for(var i = 0; i < checks.length; i++){
            if(this.checked){
                checks[i].checked = true;
                checkeds = checks.length;
            } else {
                checks[i].checked = false;
                checkeds = 0;
            }
        }
    }

    for(var x = 0; x < checks.length; x++){checks[x].onclick = verify}

    function verify(){
        if(this.checked) ++checkeds;
        else --checkeds;

        if(checkeds == checks.length){
            check.checked = true;
            //text.innerHTML = alternativeText;
        } else{
            check.checked = false;
            //text.innerHTML = defaultText;
        }
    }
}

/**
 * This array is used to remember mark status of rows in browse mode
 */
var marked_row = new Array;

/**
 * enables highlight and marking of rows in data tables
 *
 */
function PMA_markRowsInit() {
    // for every table row ...
    var rows = document.getElementsByTagName('tr');
    for ( var i = 0; i < rows.length; i++ ) {
        // ... with the class 'odd' or 'even' ...
        if ( 'odd' != rows[i].className.substr(0,3) && 'even' != rows[i].className.substr(0,4) ) {
            continue;
        }
        // ... add event listeners ...
        // ... to highlight the row on mouseover ...
        if ( navigator.appName == 'Microsoft Internet Explorer' ) {
            // but only for IE, other browsers are handled by :hover in css
            rows[i].onmouseover = function() {
                this.className += ' hover';
            }
            rows[i].onmouseout = function() {
                this.className = this.className.replace( ' hover', '' );
            }
        }
        // Do not set click events if not wanted
        if (rows[i].className.search(/noclick/) != -1) {
            continue;
        }
        // ... and to mark the row on click ...
        rows[i].onmousedown = function(event) {
            var unique_id;
            var checkbox;
            var table;

            // Somehow IE8 has this not set
            if (!event) var event = window.event

            checkbox = this.getElementsByTagName( 'input' )[0];
            if ( checkbox && checkbox.type == 'checkbox' ) {
                unique_id = checkbox.name + checkbox.value;
            } else if ( this.id.length > 0 ) {
                unique_id = this.id;
            } else {
                return;
            }

            if ( typeof(marked_row[unique_id]) == 'undefined' || !marked_row[unique_id] ) {
                marked_row[unique_id] = true;
            } else {
                marked_row[unique_id] = false;
            }

            if ( marked_row[unique_id] ) {
                this.className += ' marked';
            } else {
                this.className = this.className.replace(' marked', '');
            }

            if ( checkbox && checkbox.disabled == false ) {
                checkbox.checked = marked_row[unique_id];
                if (typeof(event) == 'object') {
                    table = this.parentNode;
                    i = 0;
                    while (table.tagName.toLowerCase() != 'table' && i < 20) {
                        i++;
                        table = table.parentNode;
                    }

                    if (event.shiftKey == true && table.lastClicked != undefined) {
                        if (event.preventDefault) {event.preventDefault();} else {event.returnValue = false;}
                        i = table.lastClicked;

                        if (i < this.rowIndex) {
                            i++;
                        } else {
                            i--;
                        }

                        while (i != this.rowIndex) {
                            table.rows[i].onmousedown();
                            if (i < this.rowIndex) {
                                i++;
                            } else {
                                i--;
                            }
                        }
                    }

                    table.lastClicked = this.rowIndex;
                }
            }
        }

        // ... and disable label ...
        var labeltag = rows[i].getElementsByTagName('label')[0];
        if ( labeltag ) {
            labeltag.onclick = function() {
                return false;
            }
        }
        // .. and checkbox clicks
        var checkbox = rows[i].getElementsByTagName('input')[0];
        if ( checkbox ) {
            checkbox.onclick = function() {
                // opera does not recognize return false;
                this.checked = ! this.checked;
            }
        }
    }
}
/*window.onload=PMA_markRowsInit;*/

/**
 * marks all rows and selects its first checkbox inside the given element
 * the given element is usaly a table or a div containing the table or tables
 *
 * @param    container_id    DOM element
 */
function markAllRows( container_id ) {
    var rows = document.getElementById(container_id).getElementsByTagName('tr');
    var unique_id;
    var checkbox;

    for ( var i = 0; i < rows.length; i++ ) {

        checkbox = rows[i].getElementsByTagName( 'input' )[0];

        if ( checkbox && checkbox.type == 'checkbox' ) {
            unique_id = checkbox.name + checkbox.value;
            if ( checkbox.disabled == false ) {
                checkbox.checked = true;
                if ( typeof(marked_row[unique_id]) == 'undefined' || !marked_row[unique_id] ) {
                    rows[i].className += ' marked';
                    marked_row[unique_id] = true;
                }
            }
        }
    }

    return true;
}

/**
 * marks all rows and selects its first checkbox inside the given element
 * the given element is usaly a table or a div containing the table or tables
 *
 * @param    container_id    DOM element
 */
function unMarkAllRows( container_id ) {
    var rows = document.getElementById(container_id).getElementsByTagName('tr');
    var unique_id;
    var checkbox;

    for ( var i = 0; i < rows.length; i++ ) {

        checkbox = rows[i].getElementsByTagName( 'input' )[0];

        if ( checkbox && checkbox.type == 'checkbox' ) {
            unique_id = checkbox.name + checkbox.value;
            checkbox.checked = false;
            rows[i].className = rows[i].className.replace(' marked', '');
            marked_row[unique_id] = false;
        }
    }

    return true;
}


function limpa_campos_aud() {

    id('det').value = '';
    id('data_aud_in').value = '';
    id('data_aud_out').value = '';
    id('local_aud').value = '';
    id('cidade_aud').value = '';
    id('data_fut').checked = true;
    id('tipo_sit').value = '';
    id('sitaud_3').checked = true;

}

function limpa_campos_proc() {

    id('det').value = '';
    id('data_del_in').value = '';
    id('data_del_out').value = '';
    id('data_sent_in').value = '';
    id('data_sent_out').value = '';
    id('numinq').value = '';
    id('numproc').value = '';
    id('comarca').value = '';
    id('tipo_sit').value = '';
    id('preso_2').checked = true;
    id('ext_2').checked = true;
    id('outroest_2').checked = true;
    id('fed_2').checked = true;

}

function seleciona(valor) {
    opener.id('matricula').value = valor;
    self.window.close();
}

function envia_tv(id) {
    saida.innerHTML = "<form action='vinculatv.php' method='post' name='form'><input type='hidden' name='idtv' id='idtv' value="+id+"></form>";
    document.form.submit();
}

function envia_radio(id) {
    saida.innerHTML = "<form action='vincularadio.php' method='post' name='form'><input type='hidden' name='idradio' id='idradio' value="+id+"></form>";
    document.form.submit();
}

function copy_matr( matr, sem_ponto ){

    copied_matr = matr;//.innerText;

    if( sem_ponto ) {
        copied_matr = copied_matr.replace(/[.-]/g,'');
    }

    window.clipboardData.setData('Text', copied_matr);

}

function CopyToClipboard(){

   CopiedTxt = campo_result.innerText;
   window.clipboardData.setData('Text', CopiedTxt);

}

function gera_matr_d( matr ){

    var saida;

    if ( matr == '' ) {

        saida = '';

        return result.innerHTML = saida;

    }

    matr = matr.replace(/[.-]/g,'');

    digito = caldmatr(matr);

    saida = '<p class="result_dig">';
    saida += '<span id="campo_result">';
    saida += matr + "-" + digito;
    saida += '</span>';
    saida += '</p>';
    saida += '<div class="form_bts">';
    saida += '<input class="form_bt" name="" type="button" onClick="CopyToClipboard()" value="Copiar" />';
    saida += '<input class="form_bt" name="" type="button" onClick="CopyToClipboard();self.window.close()" value="Copiar e fechar" />';
    saida += '</div>';

    id('matricula').focus();

    return result.innerHTML = saida;

}

function gera_rg_d( rg ){

    var saida;

    if ( rg == '' ) {

        saida = '';

        return result.innerHTML = saida;

    }

    rg = rg.replace(/[.-]/g,'');

    digito = caldrg(rg);

    saida = '<p class="result_dig">';
    saida += '<span id="campo_result">';
    saida += rg + "-" + digito;
    saida += '</span>';
    saida += '</p>';
    saida += '<div class="form_bts">';
    saida += '<input class="form_bt" name="" type="button" onClick="CopyToClipboard()" value="Copiar" />';
    saida += '<input class="form_bt" name="" type="button" onClick="CopyToClipboard();self.window.close()" value="Copiar e fechar" />';
    saida += '</div>';

    id('rg').focus();

    return result.innerHTML = saida;

}

function confirm_canc_receb(id){

    var acao = confirm('Deseja realmente cancelar o recebimento deste documento?');

    if ( acao == true ) {
        acao = envia_canc_receb(id);
    }

    return acao;

}

function envia_canc_receb(id) {

    saida.innerHTML = "<form action='../send/sendprotdesp.php' method='post' name='form'><input type='hidden' name='prot[]' value="+id+"><input type='hidden' name='crb' value='1'></form>";
    document.form.submit();

}

function confirm_canc_desp(id){

    var acao = confirm("Deseja realmente cancelar o despacho deste documento?\nIsso também cancelará o recebimento do documento!");

    if ( acao == true ) {
        acao = envia_canc_desp(id);
    }

    return acao;

}

function envia_canc_desp(id) {

    saida.innerHTML = "<form action='../send/sendprotdesp.php' method='post' name='form'><input type='hidden' name='prot[]' value="+id+"><input type='hidden' name='cdp' value='1'></form>";
    document.form.submit();

}

function confirm_desp_prot(id){

    var acao = confirm('Deseja realmente despachar este documento?');

    if ( acao == true ) {
        acao = envia_desp_prot(id);
    }

    return acao;

}

function envia_desp_prot(id) {

    saida.innerHTML = "<form action='../send/sendprotdesp.php' method='post' name='form'><input type='hidden' name='prot[]' value="+id+"><input type='hidden' name='dps' value='1'></form>";
    document.form.submit();

}

function confirm_receb_prot(id){

    var acao = confirm('Deseja realmente receber este documento?');

    if ( acao == true ) {
        acao = envia_receb_prot(id);
    }

    return acao;

}

function envia_receb_prot(id) {

    saida.innerHTML = "<form action='../send/sendprotdesp.php' method='post' name='form'><input type='hidden' name='prot[]' value="+id+"><input type='hidden' name='rec' value='1'></form>";
    document.form.submit();

}

function reat_visit( iddet, idvisit ){

    if ( ( iddet == '' || idvisit == '' ) || ( isNaN( iddet ) || isNaN( idvisit ) ) ) {
        return false;
    }

    var acao = confirm('Deseja realmente reativar este visitante?');

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/sendvisit.php' method='post' id='reat_visit' name='reat_visit'>";
        form += "<input type='hidden' name='proced' value='4'>";
        form += "<input type='hidden' name='iddet' value="+iddet+" />";
        form += "<input type='hidden' name='idvisit' value="+idvisit+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('reat_visit').submit();

    }

    return acao;

}

function drop_visit( iddet, idvisit ){

    if ( ( iddet == '' || idvisit == '' ) || ( isNaN( iddet ) || isNaN( idvisit ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR este visitante?\n\nATENÇÃO: Você não poderá desfazer essa operação");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/sendvisit.php' method='post' id='drop_visit' name='drop_visit'>";
        form += "<input type='hidden' name='proced' value='2'>";
        form += "<input type='hidden' name='iddet' value="+iddet+" />";
        form += "<input type='hidden' name='idvisit' value="+idvisit+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('drop_visit').submit();

    }

    return acao;

}

function drop_pec( iddet, idpec ){

    if ( ( iddet == '' || idpec == '' ) || ( isNaN( iddet ) || isNaN( idpec ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR este pertence?\n\nATENÇÃO: Você não poderá desfazer essa operação");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/sendpeculio.php' method='post' name='drop_pec'>";
        form += "<input type='hidden' name='proced' value='2'>";
        form += "<input type='hidden' name='iddet' value="+iddet+" />";
        form += "<input type='hidden' name='idpec' value="+idpec+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('drop_pec').submit();

    }

    return acao;

}

function conf_pert( idpert ){

    if ( ( idpert == '' ) || ( isNaN( idpert ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente CONFIRMAR este pertence?\n\nATENÇÃO: Você não poderá desfazer essa operação");

    if ( acao == true ) {

        var form = '';
        form += "<form action='../send/sendpeculioconf.php' method='post' name='f_conf_pert' id='f_conf_pert'>";
        form += "<input type='hidden' name='cnf' value=1 />";
        form += "<input type='hidden' name='idpeculio[]' value="+idpert+" />";
        form += "</form>";

        //alert( form );
        id('saida').innerHTML = form;

        id('f_conf_pert').submit();

    }

    return acao;

}

function drop_user( iduser ){

    if ( ( iduser == '' ) || ( isNaN( iduser ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR este usuário?\n\nATENÇÃO: Você não poderá desfazer essa operação");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/senduserdel.php' method='post' id='drop_user' name='drop_user'>";
        form += "<input type='hidden' name='iduser' value="+iduser+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('drop_user').submit();

    }

    return acao;

}

function drop_sedex( idsedex ){

    if ( ( idsedex == '' ) || ( isNaN( idsedex ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR este sedex?\n\nATENÇÃO: Você não poderá desfazer essa operação");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/sendsedex.php' method='post' id='drop_sedex' name='drop_sedex'>";
        form += "<input type='hidden' name='proced' value='2'>";
        form += "<input type='hidden' name='sub_proced' value='1'>";
        form += "<input type='hidden' name='idsedex[]' value="+idsedex+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('drop_sedex').submit();

    }

    return acao;

}

function drop_mov_sedex( idmovsedex ){

    if ( ( idmovsedex == '' ) || ( isNaN( idmovsedex ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR esta movimentação?\n\nATENÇÃO: Você não poderá desfazer essa operação");

    if ( acao == true ) {

        var form = '';
        form += "<form action='../send/sendsedex.php' method='post' id='drop_mov_sedex' name='drop_mov_sedex'>";
        form += "<input type='hidden' name='proced' value='2'>";
        form += "<input type='hidden' name='sub_proced' value='2'>";
        form += "<input type='hidden' name='idmovsedex' value="+idmovsedex+" />";
        form += "</form>";

        var saida = id( 'saida' );

        saida.innerHTML = form;

        id('drop_mov_sedex').submit();

    }

    return acao;

}

function drop_local_bonde( idblocal ){

    if ( ( idblocal == '' ) || ( isNaN( idblocal ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR este local?\n\nATENÇÃO: Você também excluirá do bonde todos os detentos que estão registrados para este local.\n\n Você não poderá desfazer essa operação!");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/sendbonde.php' method='post' id='drop_local_bonde' name='drop_local_bonde'>";
        form += "<input type='hidden' name='droplocalbonde' value='1'>";
        form += "<input type='hidden' name='idblocal' value="+idblocal+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('drop_local_bonde').submit();

    }

    return acao;

}

function drop_det_bonde( idbd ){

    if ( ( idbd == '' ) || ( isNaN( idbd ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR este detento do bonde?\n\nATENÇÃO: Você não poderá desfazer essa operação!");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/sendbonde.php' method='post' id='drop_det_bonde' name='drop_det_bonde'>";
        form += "<input type='hidden' name='dropdetbonde' value='1'>";
        form += "<input type='hidden' name='idbd' value="+idbd+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('drop_det_bonde').submit();

    }

    return acao;

}

function drop_bonde( idb ){

    if ( ( idb == '' ) || ( isNaN( idb ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR este bonde?\n\nATENÇÃO: Você não poderá desfazer essa operação!");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/sendbonde.php' method='post' id='drop_bonde' name='drop_bonde'>";
        form += "<input type='hidden' name='dropbonde' value='1'>";
        form += "<input type='hidden' name='idbonde' value="+idb+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('drop_bonde').submit();

    }

    return acao;

}

function drop_escolta( uid, modo ){

    if ( ( uid == '' ) || ( isNaN( uid ) ) ) {
        return false;
    }

    if ( ( modo == '' ) || ( isNaN( modo ) ) ) {
        return false;
    }

    var text_confirm = '';

    if ( modo == 1 ) { // exclusão de local do pedido de escolta

        text_confirm = "Deseja realmente EXCLUIR este local?\n\nATENÇÃO: Você também excluirá do pedido de escolta todos os detentos que estão registrados para este local.\n\n Você não poderá desfazer essa operação!"

    } else if ( modo == 2 ) {// exclusão de detento do pedido de escolta

        text_confirm = "Deseja realmente EXCLUIR este detento do pedido de escolta?\n\nATENÇÃO: Você não poderá desfazer essa operação!"

    } else if ( modo == 3 ) {// exclusão do pedido de escolta

        text_confirm = "Deseja realmente EXCLUIR este pedido de escolta?\n\nATENÇÃO: Você não poderá desfazer essa operação!"

    }

    var acao = confirm( text_confirm );

    if ( acao == true ) {

        var proced_name  = '';
        var proced_field = '';

        if ( modo == 1 ) { // exclusão de local do pedido de escolta

            proced_name  = 'droplocalesc';
            proced_field = 'idlocalesc';

        } else if ( modo == 2 ) {// exclusão de detento do pedido de escolta

            proced_name  = 'dropdetesc';
            proced_field = 'ided';

        } else if ( modo == 3 ) {// exclusão do pedido de escolta

            proced_name  = 'dropesc';
            proced_field = 'idescolta';

        }

        var form = '';
        form += "<form action='../send/sendpesc.php' method='post' id='form_drop_escolta' name='form_drop_escolta'>";
        form += "<input type='hidden' name='"+proced_name+"' value='1' />";
        form += "<input type='hidden' name='"+proced_field+"' value="+uid+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('form_drop_escolta').submit();

    }

    return acao;

}

function drop_ord_saida( uid, modo ){

    if ( ( uid == '' ) || ( isNaN( uid ) ) ) {
        return false;
    }

    if ( ( modo == '' ) || ( isNaN( modo ) ) ) {
        return false;
    }

    var text_confirm = '';

    if ( modo == 1 ) { // exclusão de local do pedido de escolta

        text_confirm = "Deseja realmente EXCLUIR este local?\n\nATENÇÃO: Você também excluirá da ordem de saída todos os detentos que estão registrados para este local.\n\n Você não poderá desfazer essa operação!"

    } else if ( modo == 2 ) {// exclusão de detento do pedido de escolta

        text_confirm = "Deseja realmente EXCLUIR este detento da ordem de saída?\n\nATENÇÃO: Você não poderá desfazer essa operação!"

    } else if ( modo == 3 ) {// exclusão do pedido de escolta

        text_confirm = "Deseja realmente EXCLUIR esta ordem de saída?\n\nATENÇÃO: Você não poderá desfazer essa operação!"

    }

    var acao = confirm( text_confirm );

    if ( acao == true ) {

        var proced_name  = '';
        var proced_field = '';

        if ( modo == 1 ) { // exclusão de local do pedido de escolta

            proced_name  = 'droplocalos';
            proced_field = 'idlocalos';

        } else if ( modo == 2 ) {// exclusão de detento do pedido de escolta

            proced_name  = 'dropdetos';
            proced_field = 'idosd';

        } else if ( modo == 3 ) {// exclusão do pedido de escolta

            proced_name  = 'dropos';
            proced_field = 'id_ord_saida';

        }

        var form = '';
        form += "<form action='../send/sendordsaida.php' method='post' id='form_drop_ord_saida' name='form_drop_ord_saida'>";
        form += "<input type='hidden' name='"+proced_name+"' value='1' />";
        form += "<input type='hidden' name='"+proced_field+"' value="+uid+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('form_drop_ord_saida').submit();

    }

    return acao;

}

function drop_susp_visit( idv, ids ){

    if ( ( idv == '' || ids == '' ) || ( isNaN( idv ) || isNaN( ids ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR esta suspenção?\n\nATENÇÃO: Você não poderá desfazer essa operação!");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/sendsuspvisit.php' method='post' id='drop_susp_visit' name='drop_susp_visit'>";
        form += "<input type='hidden' name='proced' value='2'>";
        form += "<input type='hidden' name='idvisit' value="+idv+">";
        form += "<input type='hidden' name='idsusp' value="+ids+">";
        form += "</form>";

        saida.innerHTML = form;

        id('drop_susp_visit').submit();

    }

    return acao;

}

function drop_num_tel( idnt ){

    if ( ( idnt == '' ) || ( isNaN( idnt ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR este número de telefone?\n\nATENÇÃO: Você não poderá desfazer essa operação!");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/sendlistatel.php' method='post' id='drop_num_tel' name='drop_num_tel'>";
        form += "<input type='hidden' name='proced' value='3'>";
        form += "<input type='hidden' name='idlt_num' value="+idnt+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('drop_num_tel').submit();

    }

    return acao;

}

function drop_local_tel( idlt ){

    if ( ( idlt == '' ) || ( isNaN( idlt ) ) ) {
        return false;
    }

    var acao = confirm("Deseja realmente EXCLUIR esta localidade?\n\nATENÇÃO: Você não poderá desfazer essa operação!");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/sendlistatel.php' method='post' id='drop_local_tel' name='drop_local_tel'>";
        form += "<input type='hidden' name='proced' value='4'>";
        form += "<input type='hidden' name='idlt_local' value="+idlt+" />";
        form += "</form>";

        saida.innerHTML = form;

        id('drop_local_tel').submit();

    }

    return acao;

}

function drop( field_name, id_drop, file_name, form_name, proced ){

    if ( ( id_drop == '' ) || ( isNaN( id_drop ) ) ) {
        return false;
    }

    if ( ( proced == '' ) || ( isNaN( proced ) ) ) {
        proced = 2;
    }

    var acao = confirm("Deseja realmente EXCLUIR?\n\nATENÇÃO: Você não poderá desfazer essa operação!");

    if ( acao == true ) {

        form = '';
        form += "<form action='../send/"+file_name+".php' method='post' id='"+form_name+"' name='"+form_name+"'>";
        form += "<input type='hidden' name='proced' value="+proced+">";
        form += "<input type='hidden' name='"+field_name+"' value="+id_drop+" />";
        form += "</form>";

        saida.innerHTML = form;
        //alert( "'"+form_name+"'" );

        id( form_name ).submit();

    }

    return acao;

}