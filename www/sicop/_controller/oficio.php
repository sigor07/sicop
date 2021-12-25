<?php

/**
 * Description of oficio
 *
 * @author Rafael
 * @since 23/03/2012
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
class oficio {

    private $id_of_model;
    private $cod_setor;
    private $nome_doc;
    private $ref_doc;
    private $local_data;
    private $tipo_quali;
    private $saud_sup;
    private $texto_doc;
    private $protesto;
    private $trat_doc;
    private $ass_doc;
    private $senhoria_doc;
    private $nome_dest_doc;
    private $cargo_doc;
    private $cidade_doc;
    private $recibo_doc;

    /**
     * construtor da classe
     */
    public function __construct() {

    }



    public static function findTiposRecibo() {

        // instanciando o model
        $ofmodel = new OficioModel();

        // pegando os dados
        $query = $ofmodel->getTiposRecibo();

        if ( !$query ) return FALSE;

        return $query;

    }

    public static function findTiposTratamento() {

        // instanciando o model
        $ofmodel = new OficioModel();

        // pegando os dados
        $query = $ofmodel->getTiposTratamento();

        if ( !$query ) return FALSE;

        return $query;

    }

    public static function findModelos() {

        // instanciando o model
        $ofmodel = new OficioModel();

        // pegando os dados
        $query = $ofmodel->getModelos();

        if ( !$query ) return FALSE;

        return $query;

    }

    public function find() {

        // instanciando o model
        $ofmodel = new OficioModel();

        // pegando os dados
        $query = $ofmodel->getModelosOf();

        if ( !$query ) return FALSE;

        return $query;

    }

}

?>
