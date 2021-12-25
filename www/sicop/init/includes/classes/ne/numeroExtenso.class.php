<?php

class numeroExtenso
{
    function escrever($numero,$caps=1)
    {
        $many = array('', ' mil ',' milhões ',' bilhões ');

        $numero = strval($numero);
        $saida = "";

        if(strlen($numero)%3!=0)
        { 
            $saida .= $this->cada3(substr($numero,0,strlen($numero)%3));
            $saida .= $many[floor(strlen($numero)/3)];
        }

        for($i=0; $i<floor(strlen($numero)/3); $i++)
        {
            $saida .= $this->cada3(substr($numero,strlen($numero)%3+($i*3),3));
            if($numero[strlen($numero)%3+($i*3)]!=0)
            {
                $saida .= $many[floor(strlen($numero)/3)-1-$i];
            }
        }

        $match = array('/um mil /','/um milhões/','/um bilhões/','/ +/','/ $/','/ /','/e mil/','/e bil/');
        $replace = array('mil ','um milhão','um bilhão',' ','',' e ',' mil',' bil');
        $saida = preg_replace($match,$replace,$saida);

        if($caps)
        {
            $saida = ucwords($saida);
            $saida = preg_replace("/ E /"," e ",$saida);
        }

        return $this->saida=$saida;
    }

    function cada3($numero)
    {
        $unidades = array('um','dois','três','quatro','cinco','seis','sete','oito','nove');
        $dez = array('onze','doze','treze','catorze','quinze','dezesseis','dezessete','dezoito','dezenove');
        $dezenas = array('dez','vinte','trinta','quarenta','cinqüenta','sessenta','setenta','oitenta','noventa');
        $centenas = array ('cento ', 'duzentos ', 'trezentos ', 'quatrocentos ', 'quinhentos ', 'seiscentos ', 'setecentos ', 'oitocentos ', 'novecentos ');

        $saida = "";
        $j = strlen($numero);
        $ok = false;
        for($i=0; $i<strlen($numero); $i++)
        {
            if($j==2)
            {
                if($numero[$i]==1)
                {
                    if($numero[$i+1]==0)
                        $saida .= $dezenas[$numero[$i]-1];
                    else
                    {
                        $saida .= $dez[$numero[$i+1]-1];
                        $ok = true;
                    }
                }
                else
                {
                    if(!empty($dezenas[$numero[$i]-1]))
                        $saida .= $dezenas[$numero[$i]-1].' ';
                }
            }
            elseif(($numero[$i]!=0) AND (!$ok) AND ($j==3) AND ($numero[0]==1) AND ($numero[1]==0) AND ($numero[2]==0))
                $saida .= "cem";
            elseif(($numero[$i]!=0) AND (!$ok) AND ($j==3))
                $saida .= $centenas[$numero[0]-1];
            elseif($numero[$i]!=0 && !$ok)
                $saida .= $unidades[$numero[$i]-1];
            $j--;
        }
        return $saida;
    }

}
?>