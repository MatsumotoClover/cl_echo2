<?php
require_once 'base_model.php';

/**
 * 基底モデル
 * @author onishi
 */
class Tran_model extends Base_model
{

    /**
     * Update a record for DELETE_FLG, specified by an ID.
     *
     * @param integer $id The row's ID
     * @param array $array The data to update
     * @return bool
     * @author Jamie Rumbelow
     */
    public function delete_by_id($primary_value)
    {

        $data = array(
                    'del_flg'  => FLG_ON,
                    'modified' => $this->config->item('now'),
                );

        return $this->update($primary_value, $data);

    }

    /**
     * シリアライズしつつ base64 エンコードをする関数
     *
     * @param $array
     * @return シリアライズされたデータ
     */
    protected function _serialize_base64_encode($array) {

        $data = serialize($array);
        $data = base64_encode($data);

        return $data;

    }

    /**
     * アンシリアライズしつつ base64 エンコードをする関数
     *
     * @param $array
     * @return アンシリアライズされたデータ
     */
    protected function _unserialize_base64_decode($data) {

        $data = base64_decode($data);
        $array = unserialize($data);

        return $array;

    }

}