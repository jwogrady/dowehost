<?php
    if ( isset( $_REQUEST['domain'] ) ) {
        $domain = clean_domain($_REQUEST['domain']);
        $dns_a = dns_get_record($domain, DNS_A);
        $dns_ns = dns_get_record($domain, DNS_NS);
    }

    $aig = array(
        array( 'host' => 'host.aig1.net', 'ips' => array( '67.225.211.202', '67.225.211.204', '67.225.211.158', '67.225.211.202', '67.225.211.203', '67.225.211.159' ) ),
        array( 'host' => 'host.aig2.net', 'ips' => array( '67.225.211.205', '67.225.211.207', '67.225.211.196', '67.225.211.156', '67.225.211.205', '50.28.79.150', '67.225.211.157', '67.225.211.206' ) ),
        array( 'host' => 'host.aig3.net', 'ips' => array( '69.167.156.200', '69.167.156.243', '69.167.156.172', '69.167.139.207', '69.167.139.111', '69.167.138.185', '69.167.138.85', '69.167.138.22', '69.167.156.200', '69.167.156.201', '69.167.138.84', '69.167.138.86', '69.167.138.186', '69.167.139.206', '69.167.139.208', '69.167.156.242' ) ),
        array( 'host' => 'host.aig4.net', 'ips' => array( '50.28.39.129', '50.28.39.130', '50.28.39.75', '50.28.39.129', '50.28.39.74', '50.28.39.76' ) ),
        array( 'host' => 'host.aig5.net', 'ips' => array( '209.59.172.192', '209.59.172.195', '209.59.172.193', '209.59.143.184', '209.59.143.182', '209.59.172.192', '209.59.143.183', '209.59.143.188', '209.59.172.194' ) ),
        array( 'host' => 'flash.zonedock.com', 'ips' => array( '67.225.211.159', '45.79.5.156' ) ),
    );

    function clean_domain($domain) {
        $protocols = array('http://', 'https://', 'http://www.', 'https://www.', 'www.');
        foreach ( $protocols as $prot ) {
            $domain = str_replace($prot, '', $domain);
        }
        return $domain;
    }

    function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
                return array( 'in_host' => true, 'host' => $item['host'] );
            }
        }
        return false;
    }

    $host = in_array_r($dns_a[0]['ip'], $aig);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Do We Host</title>
        <style>
            body {
                font-size: 14px;
                font-family: 'Open Sans';
                margin: 0 1rem;
                color: #cd5606;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            p {
                margin: 0;
                padding: 0;
            }
            
            .debug {
                color: #000;
                background-color: rgba(255, 0, 0, 0.2);
                border: 1px dashed rgba(255, 0, 0, 0.5);
                border-radius: 5px;
                margin: 1rem 0;
                padding: 1rem;
            }
            .debug.info {
                background-color: rgba(0, 255, 0, 0.2);
                border: 1px dashed rgba(0, 255, 0, 0.8);
            }

            h1, h2, h3, h4 {
                text-align: center;
                margin: 0;
            }
            h1 { font-size: 3rem; }
            h2 { font-size: 8rem; }
            h3, h4 { font-size: 1.5rem; }
            .home form {
                display: flex;
                flex-direction: column;
                align-items: center;
                margin: 1rem 0;
            }
            .home form label {
                display: none;
            }
            .home form input[type="text"] {
                border: none;
                border-bottom: 1px solid #ccc;
                font-size: 1rem;
                text-align: center;
                margin: 0.3rem 0;
                width: 18rem;
            }
            .home form input[type="submit"] {
                background-color: #cd5606;
                border: none;
                color: #fff;
                padding: 0.5rem 0;
                font-size: 1rem;
                text-align: center;
                margin: 0.3rem 0;
                width: 10rem;
                transition: 1s;
            }
            .home form input[type="submit"]:hover {
                background-color: #833704;
            }
        </style>
    </head>
    <?php if ( isset( $_REQUEST['domain'] ) ) { ?>
        <body class="answer">
            <div>
                <h1>Do We Host You?</h1>
                <?php if ( $host['in_host'] ) { ?>
                    <h2>YES</h2>
                <?php } else { ?>
                    <?php if ( strpos($dns_ns[0]['target'], 'ns.cloudflare.com') ) { ?>
                        <h3>Sorry, it seems that you use Cloudflare as DNS manager</h3>
                        <h4>We can not be sure of our response</h4>
                    <?php } else { ?>
                        <h2>NO</h2>
                    <?php } ?>
                <?php } ?>
            </div>

            <?php if ( isset( $_REQUEST['debug'] ) || isset( $_REQUEST['host'] ) ) { ?>
                <div class="debug info">
                    <p>IP: <?php print_r($dns_a[0]['ip']); ?></p>
                    <?php if ( $host['in_host'] && isset( $_REQUEST['host'] ) ) { ?>
                        <p>HOST: <?php echo $host['host']; ?></p>
                    <?php } ?>
                    <?php foreach ($dns_ns as $key => $value) { ?>
                        <p>NS_<?php echo $key ?>: <?php print_r($value['target']); ?></p>
                    <?php } ?>
                </div>

                <div class="debug">
                    <p>DEBUG</p>
                    <p>DNS_A: <?php print_r($dns_a[0]); ?></p>
                    <?php foreach ($dns_ns as $key => $value) { ?>
                        <p>DNS_NS_<?php echo $key ?> => <?php print_r($value); ?></p>
                    <?php } ?>
                </div>
            <?php } ?>
        </body>
    <?php } else { ?>
        <body class="home">
            <h1>Do We Host You?</h1>
            <form action="" method="GET">
                <div>
                    <label for="domain">Domain</label>
                    <input type="text" name="domain" id="domain" placeholder="adviceinteractive.com">
                </div>
                <input type="submit" value="Search">
            </form>
        </body>
    <?php } ?>
</html>