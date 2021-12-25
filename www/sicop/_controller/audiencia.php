<?php

/**
 * Description of audiencia
 *
 * @author Rafael
 * @since 06/03/2012
 *
 * ****************************************************************************
 *
 * SICOP - Sistema de Controle de Prisional
 *
 * Sistema para controle e gerenciamento de unidades prisionais
 *
 * @author  JOSÉ RAFAEL GONÇALVES - AGENTE DE SEGURANÇA III
 * @local   CENTRO DE DETENÇÃO PROVISÓRIA DE SÃO JOSÉ DO RIO PRETO - SP
 * @since   03/01/2011
 *
 * ****************************************************************************
 */
class Audiencia {

    /**
     * o id
     * @access private
     * @var string
     */
    private $_idaud;

    /**
     * construtor da classe
     */
    public function __construct( $idaud = '' ) {

        $idaud = (int)$idaud;

        if ( !empty ( $idaud ) ) {
            $this->__set( '_idaud', $idaud );
        }

    }

    public function __set( $name, $value ) {
        return $this->$name = $value;
    }

    public function __get( $name ) {
        return $this->$name;
    }

    public function findAudPrint( $uid = array() ){

        if ( empty ( $uid ) or !is_array( $uid ) ) return FALSE;

        $uid_pieces = array();
        foreach ( $uid as $value ) {

            $value = (int)$value;
            if ( empty ( $value ) ) continue;
            $uid_pieces[] = (int)$value;

        }

        if ( empty ( $uid_pieces ) ) return FALSE;

        $uid_in = implode( ',', $uid_pieces );

        // instanciando o model
        $audmodel = new AudienciaModel();

        // pegando os dados
        $query = $audmodel->getAudPrintOf( $uid_in );

        if ( !$query ) return FALSE;

        // variavel onde será armazenado o retorno
        $ret = array();

        while ( $dados = $query->fetch_object() ) {

            $dados->matricula = !empty( $dados->matricula ) ? SicopController::formataNum( $dados->matricula ) : '';

            $dados->rg_civil = !empty( $dados->rg_civil ) ? SicopController::formataNum( $dados->rg_civil ) : '';

            $dados->execucao = !empty( $dados->execucao ) ? number_format( $dados->execucao, 0, '', '.' ) : 'N/C';

            $dados->cpf = !empty( $dados->cpf ) ? SicopController::formataNum( $dados->cpf, 2 ) : 'N/C';

            $dados->nasc_f = !empty( $dados->nasc_det_f ) ? $dados->nasc_det_f . ' - ' . $dados->idade_det . ' anos' : '';
            //$this->formataDataNasc( $dados->nasc_det_f, $dados->idade_det );

            $dados->cidade = !empty( $dados->cidade ) ? $dados->cidade . ' - ' . $dados->estado : '';

            $ret[] = $dados;

        }

        return $ret;

    }

    public function findOfModel( $uid ){

        $uid = (int)$uid;

        if ( empty ( $uid ) ) return FALSE;

        // instanciando o model
        $audmodel = new AudienciaModel();

        // pegando os dados
        $query = $audmodel->getOfModel( $uid );

        if ( !$query ) return FALSE;

        $dados = $query->fetch_object();

        return $dados;

    }

    public function upCodOf( $uid, $cod_num_of ) {

        $uid = (int)$uid;

        if ( empty ( $uid ) ) return FALSE;

        $cod_num_of = (int)$cod_num_of;

        $cod_num_of = empty( $cod_num_of ) ? 'NULL' : $cod_num_of;

        // instanciando o model
        $audmodel = new AudienciaModel();

        // pegando os dados
        $query = $audmodel->upCodOf( $uid, $cod_num_of );

        if ( !$query ) return FALSE;

        return $query;

    }




}

?>
