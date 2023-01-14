<?php

require $_SERVER['DOCUMENT_ROOT'] . '/php/app.php';

if (!isset($_POST['iN'])) {
    $ms = "No name in post";
    goto end;
}
if (!isset($_POST['iT'])) {
    $ms = "No phone in post";
    goto end;
}
if (!isset($_POST['iE'])) {
    $ms = "No email in post";
    goto end;
}
if (!isset($_POST['tA'])) {
    $ms = "No message in post";
    goto end;
}

/* -------------------------------------------------------------------------- */

if (!cset('esd')) {
    //~ if sent email cookie, not set yet
    // add 1 at the end of nt before encrypt
    $esd = enc($nt . '1', 'e');
    // set cookie email sent
    setcookie('esd', $esd, dur(60 * 60));
} else {
    //~ check if email per hour limit reached
    // decrypt email cookie
    $emc = enc($_COOKIE['esd'], 'd');
    // get last character
    $last = $emc[mb_strlen($emc) - 1];
    // if limit reached go to end
    if ($last == '2') {
        $jres = "l";
        goto end;
    } else {
        // add 2 at the end of nt before encrypt
        $esd = enc($nt . '2', 'e');
        // set cookie email sent
        setcookie('esd', $esd, dur(60 * 60));
    }
}

/* -------------------------------------------------------------------------- */

$name = clean($_POST['iN'], 'fl');
// $ccd = spe($_POST['cc'], 'd'); // use when have cc, else this below:
$ccd = "+1";
$cco = clean($ccd, false);
$phone = "+" . $cco . clean($_POST['iT'], false);
$email = clean($_POST['iE'], 'lo');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $ms = 'Email ' . $email . ' error!';
    goto end;
}
$tA = clean($_POST['tA'], false);

/* -------------------------------------------------------------------------- */

mail($email, "Contact", "\nHi " . $name . " You sent following message:\n\n"
    . $tA . "\n\nWe will get back to you shortly,\n"
    . $dm . "\n\n", $from);
sleep(2);

//~ to admin
mail($re, "Contact", "\nFrom: " . $name . "\n" . $email . "\n" . $phone . "\n"
    . $ip . "\n\nMessage:\n" . $tA . "\n\n", "From:$ae \r\nReply-To:$email");

//~ to logs
// $ms = "Contact email from: " . $name . "\n" . $phone . "\n"
//     . $ip . "\n" . $email . "\nMessage: " . $tA;

$jres = "s"; // sent

/* -------------------------------------------------------------------------- */

end:
if ($ms) {
    lg($lg, $p, $tm, $ms);
}

echo $jres;