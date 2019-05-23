<?php

$conn = oci_connect('Nicu', 'parola');
if (!$conn) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
}
$stid = oci_parse($conn, 'SELECT * FROM station');
oci_execute($stid);

echo "<table border='1'>\n";
while ($row = oci_fetch_array($stid, OCI_ASSOC+OCI_RETURN_NULLS)) {
    echo "<tr>\n";
    foreach ($row as $item) {
        echo "    <td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
    }
    echo "</tr>\n";
}
echo "</table>\n";

//// Show whoami
//        $output = shell_exec("whoami");
//        echo "<strong>WHOAMI</strong>";
//        echo "<hr/>";
//        echo "$output<br/><br/><br/><br/>";
//
//        // Show The Java Version Before Setting Environmental Variable
//        $output = shell_exec("java -version 2>&1");
//        echo "<strong>Java Version Before Setting Environmental Variable</strong>";
//        echo "<hr/>";
//        echo "$output<br/><br/><br/><br/>";
////
////        // Set Enviromental Variable
////        $JAVA_HOME = "C:\Program Files\Java\jdk1.8.0_151";
////        $PATH = "$JAVA_HOME/bin:/usr/local/bin:/usr/bin:/bin";
////        putenv("JAVA_HOME=$JAVA_HOME");
////        putenv("PATH=$PATH");
//
//
//
//        // Show The Java Version After Setting Environmental Variable
//        $output = shell_exec("java -version 2>&1");
//        echo "<strong>Java Version After Setting Environmental Variable</strong>";
//        echo "<hr/>";
//        echo $output;
// exec("java -jar test.jar", $output, $return);
//var_dump($output);
//var_dump($return);
?>