<?php

/*
 * Data de criação : 03/02/2009
 * Autor           : Daniel Flores Bastos
 * Proposta        : Formatar dados para serem inseridos no Banco de Dados e para a visualização
 * setData      = Envia DATA formatada (aaaa-mm-dd).
 * setCPF       = Envia CPF formatado(###########).
 * setCNPJ      = Envia CNPJ formatado(#############).
 * setCEP       = Envia CEP formatado(########).
 * setTelefone  = Envia Telefone formatado(#########).
 * getMoney     = Retorna um VALOR formatado (###.###,##)
 * getToUpper   = Retorna o texto todo em maiúscula(AAAAAAAAAA)
 * getToLower   = Retorna o texto todo em minúscula(aaaaaaaaaa)
 * getSmallText = Retorna um texto até determinado número de caracter
 * getData      = Retorna DATA formatada(dd/mm/aaaa).
 * getCPF       = Retorna CPF formatado(###.###.###-##).
 * getCNPJ      = Retorna CNPJ formatado(##.###.###/####-#).
 * getCEP       = Retorna CEP formatado(#####-###).
 * getTelefone  = Retorna Telefone formatado((##) ####-####).
 */

class FormataString {

    private $cpf_cnpj;
    private $data;
    private $dataHora;
    private $telefone;
    private $cep;
    private $nr_zero;
    private $zeros;

    function setData( $valor ) {
        if ( !empty( $valor ) ) {
            $this->data = explode( '/', $valor );
            $this->data = $this->data[2] . '-' . $this->data[1] . '-' . $this->data[0];
            return $this->data;
        }
    }

    function getData( $valor ) {

        if ( !empty( $valor ) ) {
            $this->data = substr( $valor, 8, 2 );
            $this->data .= '/' . substr( $valor, 5, 2 );
            $this->data .= '/' . substr( $valor, 0, 4 );
            return $this->data;
        }
    }

    function setCPF( $valor ) {

        if ( !empty( $valor ) ) {

            $this->cpf_cnpj = explode( '.', $valor );
            $this->cpf_cnpj = $this->cpf_cnpj[0] . $this->cpf_cnpj[1] . $this->cpf_cnpj[2];
            $this->cpf_cnpj = explode( '-', $this->cpf_cnpj );
            $this->cpf_cnpj = $this->cpf_cnpj[0] . $this->cpf_cnpj[1];
            if ( is_numeric( $this->cpf_cnpj ) && strlen( $this->cpf_cnpj ) == 11 )
                return $this->cpf_cnpj;
        }
    }

    function setCNPJ( $valor ) {
        $this->cpf_cnpj = explode( '.', $valor );
        $this->cpf_cnpj = $this->cpf_cnpj[0] . $this->cpf_cnpj[1] . $this->cpf_cnpj[2];
        $this->cpf_cnpj = explode( '/', $this->cpf_cnpj );
        $this->cpf_cnpj = $this->cpf_cnpj[0] . $this->cpf_cnpj[1];
        $this->cpf_cnpj = explode( '-', $this->cpf_cnpj );
        $this->cpf_cnpj = $this->cpf_cnpj[0] . $this->cpf_cnpj[1];
        return $this->cpf_cnpj;
    }

    function getCPF( $valor ) {
        $this->cpf_cnpj = substr( $valor, 0, 3 );
        $this->cpf_cnpj .= '.' . substr( $valor, 3, 3 );
        $this->cpf_cnpj .= '.' . substr( $valor, 6, 3 );
        $this->cpf_cnpj .= '-' . substr( $valor, -2 );
        return $this->cpf_cnpj;
    }

    function getCNPJ( $valor ) {
        $this->cpf_cnpj = substr( $valor, 0, 2 );
        $this->cpf_cnpj .= '.' . substr( $valor, 2, 3 );
        $this->cpf_cnpj .= '.' . substr( $valor, 5, 3 );
        $this->cpf_cnpj .= '/' . substr( $valor, 8, 4 );
        $this->cpf_cnpj .= '-' . substr( $valor, -1 );
        return $this->cpf_cnpj;
    }

    function setTelefone( $valor ) {
        $this->telefone = explode( '(', $valor );
        $this->telefone = $this->telefone[0] . $this->telefone[1];
        $this->telefone = explode( ')', $this->telefone );
        $this->telefone = $this->telefone[0] . $this->telefone[1];
        $this->telefone = explode( '-', $this->telefone );
        $this->telefone = $this->telefone[0] . $this->telefone[1];
        $this->telefone = explode( ' ', $this->telefone );
        $this->telefone = $this->telefone[0] . $this->telefone[1];
        return $this->telefone;
    }

    function getTelefone( $valor ) {
        $this->telefone = '(' . substr( $valor, 0, 2 ) . ') ';
        $this->telefone .= substr( $valor, 2, 4 ) . '-';
        $this->telefone .= substr( $valor, 6, 8 );
        return $this->telefone;
    }

    function setCEP( $valor ) {
        if ( !empty( $valor ) ) {
            $this->cep = explode( '-', $valor );
            $this->cep = $this->cep[0] . $this->cep[1];
            return $this->cep;
        }
    }

    function getCEP( $valor ) {
        if ( !empty( $valor ) ) {
            $this->cep = substr( $valor, 0, 5 ) . '-';
            $this->cep .= substr( $valor, 5, 3 );
            return $this->cep;
        }
    }

    function getSmallText( $nr_caracter, $texto ) {

        $_text = $texto;

        if ( strlen( $texto ) > $nr_caracter ) {
            $_text = substr( $texto, 0, ($nr_caracter - 3 ) ) . "...";
        }

        return $_text;
    }

    function getMoney( $valor ) {
        if ( empty( $valor ) )
            $valor = 0;

        return number_format( $valor, 2, ',', '.' );
    }

    function getToUpper( $valor ) {
        if ( !empty( $valor ) )
            $_valor = strtoupper( $valor );

        return $_valor;
    }

    function getToLower( $valor ) {
        if ( !empty( $valor ) )
            $_valor = strtolower( $valor );

        return $_valor;
    }

}

?>
