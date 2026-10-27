<?php
    header("Content-type: text/xml");
    header("refresh:5;url=http://192.168.3.250/cisco/tictac.php/");
    
    echo "<CiscoIPPhoneText>\n";
    echo "  <Title>Tic Tac Toe</Title>\n";
    echo "  <Text>";
    echo "\t\t\t\t1\t\t2\t\t3\n";
    echo "\t\t\t\t4\t\t5\t\t6\n";
    echo "\t\t\t\t7\t\t8\t\t9\n";
    echo "  </Text>\n";
    
    echo "  <Prompt>Board layout for moves.</Prompt>\n";
    echo "</CiscoIPPhoneText>";
?>
