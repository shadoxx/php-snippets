<?php
    class TokenObject {
        private $tokenString = '';
        private $propList = [];

        private $compiledToken = '';

        public function __construct($token = "AssetObject", Array $properties)
        {
            $this->tokenString = $token;
            $this->propList = $properties;
        }

        // returns the value of $this->compiledToken if sprintf() succeeds
        public function compile()
        {
            // <AssetObject src="null" mtl="null />
            foreach( $this->propList as $key=>$value ) {
                $this->tokenString .= " " . $key . '="' . $value . '"'; 
            }

            return $this->compiledToken;
        }
    }

    $myToken = new TokenObject("AssetObject", ['src' => 'null', 'mtl' => 'null' ]);
    echo $myToken->compile();
