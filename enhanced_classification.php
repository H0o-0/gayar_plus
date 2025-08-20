<?php

class EnhancedClassification {

    private $conn;
    private $brands_dict;
    private $accessories_dict;
    private $brands_from_db = [];

    public function __construct($conn) {
        $this->conn = $conn;
        $this->brands_dict = $this->get_brands_dictionary();
        $this->accessories_dict = $this->get_accessories_dictionary();

        // Pre-fetch brands from DB for mapping
        $qry = $this->conn->query("SELECT id, name FROM brands");
        while($row = $qry->fetch_assoc()){
            $this->brands_from_db[strtolower($row['name'])] = $row['id'];
        }
    }

    /**
     * Main classification function.
     * In the future, this will be expanded to handle multiple devices.
     */
    public function classifyProduct($product_name) {
        $analysis = $this->analyze_product_name_extended($product_name);

        $brand_name = $analysis['brands'] ? $analysis['brands'][0] : null; // Use the first detected brand for mapping
        $category_id = null;
        if($brand_name && isset($this->brands_from_db[strtolower($brand_name)])){
            $category_id = $this->brands_from_db[strtolower($brand_name)];
        }

        return [
            'brand' => $analysis['brands'] ? implode(' / ', $analysis['brands']) : null,
            'model' => $analysis['models'] ? implode(' / ', $analysis['models']) : null,
            'type' => $analysis['type'],
            'category_id' => $category_id,
            'sub_category_id' => null, // Model mapping to series_id is more complex
            'confidence' => !empty($analysis['brands']) ? 0.95 : 0.0 // Basic confidence score
        ];
    }

    /**
     * Analyzes the product name to find multiple brands, models, and a single type.
     */
    private function analyze_product_name_extended($product_name) {
        $product_name_lower = mb_strtolower($product_name);
        $parts = array_map('trim', explode('/', $product_name_lower));

        $found_brands = [];
        $found_models = [];
        $found_type = null;

        foreach ($parts as $part) {
            if(empty($part)) continue;

            // Search for brand and model in each part
            foreach ($this->brands_dict as $brand => $data) {
                foreach ($data['patterns'] as $pattern) {
                    if (mb_strpos($part, mb_strtolower($pattern)) !== false) {
                        if (!in_array(ucfirst($brand), $found_brands)) {
                            $found_brands[] = ucfirst($brand);
                        }

                        if (isset($data['models'])) {
                            foreach ($data['models'] as $model => $model_patterns) {
                                foreach ($model_patterns as $model_pattern) {
                                    if (mb_strpos($part, mb_strtolower($model_pattern)) !== false) {
                                        if (!in_array(ucfirst($model), $found_models)) {
                                            $found_models[] = ucfirst($model);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Search for accessory type in the full name (usually it's mentioned once)
        foreach ($this->accessories_dict as $type => $data) {
            foreach ($data['patterns'] as $pattern) {
                if (mb_strpos($product_name_lower, mb_strtolower($pattern)) !== false) {
                    $found_type = $type;
                    break 2;
                }
            }
        }

        return [
            'brands' => $found_brands,
            'models' => $found_models,
            'type' => $found_type
        ];
    }

    /**
     * Returns the dictionary of brands and their patterns.
     * Adapted from csv_reader_advanced.php
     */
    private function get_brands_dictionary() {
        return [
            'apple' => [
                'patterns' => ['iph', 'ip', 'iphone', 'ios', 'ايفون', 'ابل', 'apple'],
                'models' => [
                    '7' => ['iphone 7', 'ايفون 7', 'ip7'],
                    '8' => ['iphone 8', 'ايفون 8', 'ip8'],
                    'x' => ['iphone x', 'ايفون x', 'ipx'],
                    '11' => ['iphone 11', 'ايفون 11', 'ip11'],
                    '12' => ['iphone 12', 'ايفون 12', 'ip12'],
                    '13' => ['iphone 13', 'ايفون 13', 'ip13'],
                    '14' => ['iphone 14', 'ايفون 14', 'ip14'],
                    '15' => ['iphone 15', 'ايفون 15', 'ip15'],
                    '16' => ['iphone 16', 'ايفون 16', 'ip16']
                ]
            ],
            'samsung' => [
                'patterns' => ['sam', 'galaxy', 'gal', 'note', 'سام', 'سامسونج', 'samsung'],
                'models' => [
                    's20' => ['galaxy s20', 's20', 'سامسونج s20'],
                    's21' => ['galaxy s21', 's21', 'سامسونج s21'],
                    's22' => ['galaxy s22', 's22', 'سامسونج s22'],
                    's23' => ['galaxy s23', 's23', 'سامسونج s23'],
                    'note20' => ['note 20', 'note20', 'نوت 20'],
                    'a52' => ['galaxy a52', 'a52', 'سامسونج a52'],
                    'a53' => ['galaxy a53', 'a53', 'سامسونج a53'],
                    'a54' => ['galaxy a54', 'a54', 'سامسونج a54']
                ]
            ],
            'huawei' => [
                'patterns' => ['hua', 'hw', 'mate', 'p', 'هواوي', 'huawei'],
                'models' => [
                    'p30' => ['p30', 'p 30', 'هواوي p30'],
                    'p40' => ['p40', 'p 40', 'هواوي p40'],
                    'p50' => ['p50', 'p 50', 'هواوي p50'],
                    'mate30' => ['mate 30', 'mate30', 'ميت 30'],
                    'mate40' => ['mate 40', 'mate40', 'ميت 40'],
                    'mate50' => ['mate 50', 'mate50', 'ميت 50']
                ]
            ],
            'xiaomi' => [
                'patterns' => ['xia', 'mi', 'redmi', 'شاومي', 'ريدمي', 'xiaomi'],
                'models' => [
                    'mi11' => ['mi 11', 'mi11', 'شاومي 11'],
                    'mi12' => ['mi 12', 'mi12', 'شاومي 12'],
                    'redmi10' => ['redmi 10', 'redmi10', 'ريدمي 10'],
                    'redmi11' => ['redmi 11', 'redmi11', 'ريدمي 11'],
                    'poco' => ['poco', 'بوكو']
                ]
            ],
            'oppo' => [
                'patterns' => ['opp', 'op', 'reno', 'أوبو', 'اوبو', 'oppo'],
                'models' => [
                    'reno5' => ['reno 5', 'reno5', 'رينو 5'],
                    'reno6' => ['reno 6', 'reno6', 'رينو 6'],
                    'reno7' => ['reno 7', 'reno7', 'رينو 7'],
                    'find' => ['find', 'فايند']
                ]
            ],
            'vivo' => [
                'patterns' => ['viv', 'فيفو', 'vivo'],
                'models' => [
                    'v20' => ['v20', 'v 20', 'فيفو 20'],
                    'v21' => ['v21', 'v 21', 'فيفو 21'],
                    'v23' => ['v23', 'v 23', 'فيفو 23'],
                    'y20' => ['y20', 'y 20', 'واي 20']
                ]
            ],
            'oneplus' => [
                'patterns' => ['onep', 'ون بلس', 'oneplus'],
                'models' => [
                    '9' => ['oneplus 9', 'ون بلس 9'],
                    '10' => ['oneplus 10', 'ون بلس 10'],
                    '11' => ['oneplus 11', 'ون بلس 11']
                ]
            ],
            'lg' => [
                'patterns' => ['lg'],
                'models' => [
                    'g8' => ['g8', 'g 8'],
                    'v60' => ['v60', 'v 60']
                ]
            ],
            'sony' => [
                'patterns' => ['son', 'xperia', 'سوني', 'sony'],
                'models' => [
                    'xperia1' => ['xperia 1', 'xperia1', 'اكسبيريا 1'],
                    'xperia5' => ['xperia 5', 'xperia5', 'اكسبيريا 5'],
                    'xperia10' => ['xperia 10', 'xperia10', 'اكسبيريا 10']
                ]
            ],
            'realme' => [
                'patterns' => ['real', 'realme', 'ريلمي'],
                'models' => []
            ],
            'infinix' => [
                'patterns' => ['inf', 'infinix', 'انفنكس'],
                'models' => []
            ],
            'tecno' => [
                'patterns' => ['tec', 'tecno', 'تكنو'],
                'models' => []
            ]
        ];
    }

    /**
     * Returns the dictionary of accessory types.
     * Adapted from csv_reader_advanced.php
     */
    private function get_accessories_dictionary() {
        return [
            'screen' => [
                'patterns' => ['lcd', 'display', 'scr', 'شاشة', 'screen'],
                'description' => 'شاشات الاستبدال'
            ],
            'battery' => [
                'patterns' => ['batt', 'battery', 'bat', 'بطارية'],
                'description' => 'بطاريات الهواتف'
            ],
            'charger' => [
                'patterns' => ['chg', 'charger', 'شاحن'],
                'description' => 'شواحن وكابلات'
            ],
            'cable' => [
                'patterns' => ['cab', 'cable', 'كابل'],
                'description' => 'كابلات البيانات والشحن'
            ],
            'case' => [
                'patterns' => ['case', 'cover', 'جراب', 'غطاء'],
                'description' => 'جرابات الحماية'
            ],
            'protector' => [
                'patterns' => ['screen', 'protector', 'واقي'],
                'description' => 'واقيات الشاشة'
            ]
        ];
    }
}
