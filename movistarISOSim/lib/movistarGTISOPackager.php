<?php
class isoPack {
    private $DATA_ELEMENT	= array (
        1	=> array('b', 64, 0),
        2	=> array('n', 19, 2),
        3	=> array('n', 6, 0),
        4	=> array('n', 12, 0),
        5	=> array('n', 12, 0),
        6	=> array('n', 12, 0),
        7	=> array('n', 10, 0),
        8	=> array('n', 8, 0),
        9	=> array('n', 8, 0),
        10	=> array('n', 8, 0),
        11	=> array('n', 6, 0),
        12	=> array('n', 6, 0),
        13	=> array('n', 4, 0),
        14	=> array('n', 4, 0),
        15	=> array('n', 4, 0),
        16	=> array('n', 4, 0),
        17	=> array('n', 4, 0),
        18	=> array('n', 4, 0),
        19	=> array('n', 3, 0),
        20	=> array('n', 3, 0),
        21	=> array('n', 3, 0),
        22	=> array('n', 3, 0),
        23	=> array('n', 4, 0),
        24	=> array('n', 3, 0),
        25	=> array('n', 2, 0),
        26	=> array('n', 2, 0),
        27	=> array('n', 1, 0),
        28	=> array('n', 8, 0),
        29	=> array('an', 9, 0),
        30	=> array('n', 8, 0),
        31	=> array('an', 2, 0),
        32	=> array('n', 11, 1),
        33	=> array('n', 11, 1),
        34	=> array('an', 28, 1),
        35	=> array('z', 37, 1),
        36	=> array('n', 104, 1),
        37	=> array('an', 12, 0),
        38	=> array('an', 6, 0),
        39	=> array('an', 2, 0),
        40	=> array('an', 3, 0),
        41	=> array('ans', 9, 4),
        42	=> array('ans', 15, 3),
        43	=> array('ans', 40, 0),
        44	=> array('an', 25, 1),
        45	=> array('an', 76, 1),
        46	=> array('an', 999, 1),
        47	=> array('an', 999, 1),
        48	=> array('ans', 119, 1),
        49	=> array('n', 4, 0),
        50	=> array('an', 3, 0),
        51	=> array('a', 3, 0),
        52	=> array('an', 16, 0),
        53	=> array('an', 18, 0),
        54	=> array('an', 120, 0),
        55	=> array('ans', 999, 2),
        56	=> array('ans', 999, 1),
        57	=> array('ans', 999, 1),
        58	=> array('ans', 999, 1),
        59	=> array('ans', 9, 5),
        60	=> array('ans', 60, 1),
        61	=> array('ans', 99, 1),
        62	=> array('ans', 999, 1),
        63	=> array('ans', 999, 2),
        64	=> array('b', 16, 0),
        65	=> array('b', 16, 0),
        66	=> array('n', 1, 0),
        67	=> array('n', 2, 0),
        68	=> array('n', 3, 0),
        69	=> array('n', 3, 0),
        70	=> array('n', 3, 0),
        71	=> array('n', 4, 0),
        72	=> array('ans', 999, 1),
        73	=> array('n', 6, 0),
        74	=> array('n', 10, 0),
        75	=> array('n', 10, 0),
        76	=> array('n', 10, 0),
        77	=> array('n', 10, 0),
        78	=> array('n', 10, 0),
        79	=> array('n', 10, 0),
        80	=> array('n', 10, 0),
        81	=> array('n', 10, 0),
        82	=> array('n', 12, 0),
        83	=> array('n', 12, 0),
        84	=> array('n', 12, 0),
        85	=> array('n', 12, 0),
        86	=> array('n', 15, 0),
        87	=> array('an', 16, 0),
        88	=> array('n', 16, 0),
        89	=> array('n', 16, 0),
        90	=> array('an', 42, 0),
        91	=> array('an', 1, 0),
        92	=> array('n', 2, 0),
        93	=> array('n', 5, 0),
        94	=> array('an', 7, 0),
        95	=> array('an', 42, 0),
        96	=> array('an', 8, 0),
        97	=> array('an', 17, 0),
        98	=> array('ans', 25, 0),
        99	=> array('n', 11, 1),
        100	=> array('n', 11, 1),
        101	=> array('ans', 17, 0),
        102	=> array('ans', 28, 1),
        103	=> array('ans', 28, 1),
        104	=> array('an', 99, 1),
        105	=> array('ans', 999, 1),
        106	=> array('ans', 999, 1),
        107	=> array('ans', 999, 1),
        108	=> array('ans', 999, 1),
        109	=> array('ans', 999, 1),
        110	=> array('ans', 999, 1),
        111	=> array('ans', 999, 1),
        112	=> array('ans', 999, 1),
        113	=> array('n', 11, 1),
        114	=> array('ans', 999, 1),
        115	=> array('ans', 999, 1),
        116	=> array('ans', 999, 1),
        117	=> array('ans', 999, 1),
        118	=> array('ans', 999, 1),
        119	=> array('ans', 999, 1),
        120	=> array('ans', 999, 1),
        121	=> array('ans', 999, 1),
        122	=> array('ans', 999, 1),
        123	=> array('ans', 999, 1),
        124	=> array('ans', 255, 1),
        125	=> array('ans', 50, 1),
        126	=> array('ans', 6, 1),
        127	=> array('ans', 999, 1),
        128	=> array('b', 16, 0)
    );
    
    private $_data = [];
    private $_bitmap = '';
    private $_mti = '';
    private $_iso = '';
    private $_valid = [];


    
    // Functions -------------------------------------------------------------
    
    //format data element
    private function _packElement($data_element, $data) {
        $result	= "";

        //Numeric
        if ($data_element[0] == 'n' && is_numeric($data) && strlen($data) <= $data_element[1]) {
            $data = str_replace(".", "", $data);
            switch ($data_element[2]) {
                case 1:
                    $result = str_pad($data, $data_element[1], "0", STR_PAD_RIGHT);
                    break;
                case 2:
                    $result = str_pad(strval(strlen($data)),strlen($data_element[1]), "0", STR_PAD_LEFT).$data;
                    break;
                default:
                    $result = str_pad($data, $data_element[1], "0", STR_PAD_LEFT);
                    break;
            }
        }

        //Alpha
        if (($data_element[0]=='a' && ctype_alpha($data) && strlen($data)<=$data_element[1]) ||
            ($data_element[0]=='an' && ctype_alnum($data) && strlen($data)<=$data_element[1]) ||
            ($data_element[0]=='z' && strlen($data)<=$data_element[1]) ||
            ($data_element[0]=='ans' && strlen($data)<=$data_element[1])) {
            switch ($data_element[2]){
                case 1:
                    $result = str_pad($data, $data_element[1], "0", STR_PAD_LEFT);
                    break;
                case 2:
                    $result = str_pad(strval(strlen($data)),strlen($data_element[1]), "0", STR_PAD_LEFT).$data;
                    break;
                case 3:
                    $result = str_pad($data, $data_element[1], "0", STR_PAD_RIGHT);
                    break;
                case 4:
                    $result = $data;
                    break;
                case 5:
                    $result = str_pad(strval(strlen($data)),strlen($data_element[1]), " ", STR_PAD_LEFT).$data;
                    break;
                default:
                    $result = $result = str_pad($data, $data_element[1], " ", STR_PAD_RIGHT);;
                    break;
            }
         }

        //Bit
        if ($data_element[0]=='b' && strlen($data)<=$data_element[1]) {

            if ($data_element[2]==0) {
                $tmp	= sprintf("%0". $data_element[1] ."d", $data);

                while ($tmp!='') {
                    $result	.= base_convert(substr($tmp, 0, 4), 2, 16);
                    $tmp	= substr($tmp, 4, strlen($tmp)-4);
                }
            }
        }

        return $result;
    }

    //Calcula el Bitmap    
    private function _calculateBitmap() {	
        $tmp	= sprintf("%064d", 0);    
        $tmp2	= sprintf("%064d", 0);    
        foreach ($this->_data as $key=>$val) {
            if ($key<65) {
                $tmp[$key-1]	= 1;
            }
            else {
                $tmp[0]	= 1;
                $tmp2[$key-65]	= 1;
            }
        }
        
        $result	= "";
        if ($tmp[0]==1) {
            while ($tmp2!='') {
                $result	.= base_convert(substr($tmp2, 0, 4), 2, 16);
                $tmp2	= substr($tmp2, 4, strlen($tmp2)-4);
            }
        }
        $main	= "";
        while ($tmp!='') {
            $main	.= base_convert(substr($tmp, 0, 4), 2, 16);
            $tmp	= substr($tmp, 4, strlen($tmp)-4);
        }
        $this->_bitmap	= strtoupper($main. $result);
        
        return $this->_bitmap;
    }
    
    
    //Message Type 
    private function _parseMTI() {
        $this->addMTI(substr($this->_iso, 0, 4));
        if (strlen($this->_mti)==4 && $this->_mti[1]!=0) {
            $this->_valid['mti'] = true;
        }
    }

    //Clear Data
    private function _clear() {
        $this->_mti = '';
        $this->_bitmap = '';
        $this->_data = [];
        $this->_iso = '';
    }

    //Get Bitmap    
    private function _parseBitmap() {
        $this->_valid['bitmap']	= false;
        $inp = substr($this->_iso, 4, 32);
        $primary = '';
        $secondary = '';
        for ($i=0; $i<16; $i++) {
            $primary .= sprintf("%04d", base_convert($inp[$i], 16, 2));
        }
        if ($primary[0]==1) {
            for ($i=16; $i<32; $i++) {
                $secondary .= sprintf("%04d", base_convert($inp[$i], 16, 2));
            }
            $this->_valid['bitmap'] = true;
            $this->_bitmap = $primary.$secondary;
        }else {
            $this->_valid['bitmap'] = true;
            $this->_bitmap = $primary;
        }
        $this->_data[1] = '';
        for ($i=0; $i<strlen($this->_bitmap); $i++) {
            if ($this->_bitmap[$i]==1) {
                $bitPlace = $i+1;
                $this->_data[$bitPlace] = '?';                
            }
        }
        return $this->_bitmap;
    }

    //Decode ISO to Array
    private function _parseData() {
        if ($this->_data[1] == '?') {
            $inp	= substr($this->_iso, 4+32, strlen($this->_iso)-4-32);
        }
        else {
            $inp	= substr($this->_iso, 4+16, strlen($this->_iso)-4-16);

        }

        if (is_array($this->_data)) {
          $this->_valid['data']	= true;
          foreach ($this->_data as $key=>$val) {
            $this->_valid['de'][$key]	= false;
            if ($this->DATA_ELEMENT[$key][0]!='b') {

                if ($this->DATA_ELEMENT[$key][2]==0) {
                    $tmp	= substr($inp, 0, $this->DATA_ELEMENT[$key][1]);
                    if (strlen($tmp)==$this->DATA_ELEMENT[$key][1]) {
                        if ($this->DATA_ELEMENT[$key][0]=='n') {
                            $this->_data[$key]	= substr($inp, 0, $this->DATA_ELEMENT[$key][1]);
                        }
                        else {
                            $this->_data[$key]	= ltrim(substr($inp, 0, $this->DATA_ELEMENT[$key][1]));
                        }
                        $this->_valid['de'][$key]	= true;
                        $inp	= substr($inp, $this->DATA_ELEMENT[$key][1], strlen($inp)-$this->DATA_ELEMENT[$key][1]);
                    }
                }

                else {
                    $len	= strlen($this->DATA_ELEMENT[$key][1]);
                    $tmp	= substr($inp, 0, $len);
                    if (strlen($tmp)==$len ) {
                        $num	= (integer) $tmp;
                        $inp	= substr($inp, $len, strlen($inp)-$len);
                    
                        $tmp2	= substr($inp, 0, $num);
                        if (strlen($tmp2)==$num) {
                            if ($this->DATA_ELEMENT[$key][0]=='n') {
                                $this->_data[$key]	= (double) $tmp2;
                            }
                            else {
                                $this->_data[$key]	= ltrim($tmp2);
                            }
                            $inp	= substr($inp, $num, strlen($inp)-$num);
                            $this->_valid['de'][$key]	= true;
                        }
                    }
                    
                }
            }
            else {
                if ($key>1) {

                    if ($this->DATA_ELEMENT[$key][2]==0) {
                        $start	= false;
                        for ($i=0; $i<$this->DATA_ELEMENT[$key][1]/4; $i++) {                        
                            $bit	= base_convert($inp[$i], 16, 2);
                            
                            if ($bit!=0) $start	= true;
                            if ($start) $this->_data[$key]	.= $bit;
                        }
                        $this->_data[$key]	= $bit;
                    }
                }
                else {
                    $tmp = substr($this->_iso, 4+16, 16);
                    if (strlen($tmp)==16) {
                        $this->_data[$key]	= substr($this->_iso, 4+16, 16);
                        $this->_valid['de'][$key]	= true;
                    }
                }
            }
            if (!$this->_valid['de'][$key]) $this->_valid['data']	= false;
          }
        }

        return $this->_data;
    }
    
    /* -----------------------------------------------------
        Methods
       ----------------------------------------------------- */

    //Add Fields
    public function addData($bit, $data) {
        if ($bit>1 && $bit<129) {
            $this->_data[$bit]	= $this->_packElement($this->DATA_ELEMENT[$bit], $data);
            ksort($this->_data);
            $this->_calculateBitmap();
        }
    }

    //Add Message Type
    public function addMTI($mti) {
        if (strlen($mti)==4 && ctype_digit($mti)) {
            $this->_mti	= $mti;
        }
    }	 
    

    //Get Data Array
    public function getData() {
        return $this->_data;
    }
    
    //Get Bit
    public function getBit($bitID) {
        return $this->_data[$bitID];
    }

    //Get Bitmap
    public function getBitmap() {
        return $this->_bitmap;
    }

    //Get Message Type
    public function getMTI() {
        return $this->_mti;
    }

    //Get Full ISO
    public function getISO() {
        $this->_iso	= $this->_mti. $this->_bitmap. implode($this->_data);
        return $this->_iso;
    }
         
    //Push ISO String
    public function addISO($iso) {
        $this->_clear();
        if ($iso!='') {
            $this->_iso	= $iso;    
            $this->_parseMTI();
            $this->_parseBitmap();
            $this->_parseData();
            var_dump($this->_iso);
            var_dump($this->_data);
            foreach ($this->_data as $key=>$val){
                echo "[bit: $key] ==> ".json_encode($this->DATA_ELEMENT[$key])."\n";
            }
        }
    }
    
    //Validate ISO Format
    public function validateISO() {
        return $this->_valid['mti'] && $this->_valid['bitmap'] && $this->_valid['data'];
    }
    
    //Delete Field
    public function removeData($bit) {
        if ($bit>1 && $bit<129) {
            unset($this->_data[$bit]);
            ksort($this->_data);            
            $this->_calculateBitmap();
        }
    }
    
}

?>