<?php
    # Program: tictac.php
    #    Date: 12-Jul-2020
    #  Author: gerry.walterscheid@gmail.com
    # Descrip: Play the game of Tic Tac Toe against the computer as easy, medium, or hard.

    session_start();
    header("Content-type: text/xml");


    # Recall prior variable state
    $id = $_SESSION['id'] ?? "";                  # App name.

    # Prevent data corruption to next application.
	if ($id == "tictac") {
        // Recall prior variable state;
        $bd = $_SESSION['bd'];          # Game board.
        $move = $_SESSION['move'];      # Human player move.
        $player = $_SESSION['player'];  # Last player.
        $state = $_SESSION['state'];    # Game state.
        $mode = $_SESSION['mode'];      # Game difficulty.
        $mesg = ""; }

    else 
        $bd = array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 ); 
    
    // Get SoftKey selection and move position.
    $cmd = $_GET['key'] ?? 0;
    $move = $_GET['move'] ?? 0; 

    const HUMAN = 1;
    const TIE = 0;
    const COMPUTER = -1;
    
    
    // A function to show X or O on the board.
    function token($cell)
    {
        switch($cell) {
            case -1 :
                return "O";
            case  0 :
                return "-";
            case  1 :
                return "X";
        }
        return;
    }
    
    
    // Initialize the game board.
    function initBoard($bd)
    {
        // Initialize the game board (moveCount is saved in element 0).
        for ($i = 0; $i < 10; $i++)
            $bd[$i] = 0;
        
        return array($bd);
    }
    
    
    // Check the state of the game.
    function win($bd, $player)
    {
        $playerWin = $player * 3;
        
        // Check for a winning row.
        if (($bd[1] + $bd[2] + $bd[3] == $playerWin) ||
            ($bd[4] + $bd[5] + $bd[6] == $playerWin) ||
            ($bd[7] + $bd[8] + $bd[9] == $playerWin))
            return $player;
        
        // Check for a winning column.
        if (($bd[1] + $bd[4] + $bd[7] == $playerWin) ||
            ($bd[2] + $bd[5] + $bd[8] == $playerWin) ||
            ($bd[3] + $bd[6] + $bd[9] == $playerWin))
            return $player;
        
        // Check for a winning diagonal.
        if (($bd[1] + $bd[5] + $bd[9] == $playerWin) ||
            ($bd[3] + $bd[5] + $bd[7] == $playerWin))
            return $player;
        
        return 0;
    }
    
    
    // Get open center on board.
    function getCenter($bd)
    {
        if ($bd[5] == 0)
            return 5;
        else
            return 0;
    }
    
    
    // Get open corner on board.
    function getCorner($bd)
    {
        foreach(array(1, 3, 7, 9) as $i)
            if ($bd[$i] == 0)
                $avail[] = $i;
        
        return $avail[mt_rand(0, count($avail) - 1)] + 0;
    }
    
    
    // Get open edge on board.
    function getEdge($bd)
    {
        foreach(array(2, 4, 6, 8) as $i)
            if ($bd[$i] == 0)
                $avail[] = $i;
        
        return $avail[mt_rand(0, count($avail) - 1)] + 0;
    }

    
    // Get next available move.
    function nextAvail($bd)
    {
        for ($i = 1; $i < 10; $i++)
            if ($bd[$i] == 0)
                $avail[] = $i;
        
        return $avail[mt_rand(0, count($avail) - 1)] + 0;
    }

    
    // Is the human on opposite corners?
    function oppCorners($bd)
    {
        $result = FALSE;
        
        if ((($bd[1] == $bd[9]) && ($bd[1] == HUMAN)) ||
            (($bd[3] == $bd[7]) && ($bd[3] == HUMAN)))
            $result = TRUE;
        
        return $result;
    }
    
    
    // Find winning move, if one exists.
    function findWin($bd, $player)
    {
        for ($i = 1; $i < 10; $i++)
        {
            if ($bd[$i] != 0)
                continue;
            
            $bd[$i] = $player;
            if (win($bd, $player))
                return $i;
            
            $bd[$i] = 0;
        }
        return 0;
    }
    
    
    // Get human move.
    function humanMove($bd, $move)
	{
		$move = (int) $move;

        // If the spot is free and valid, accept the move.
		if (($move > 0) && ($move < 10) && ($bd[$move] == 0))
            return $move;
        
        return 0;
    }
    
    
    // Get computer move.
    function computerMove($bd, $mode)
    {
        // Use mode to determine level of computer skill.
        // 1 = Easy, 2 = Medium, 3 = Hard
        
        // Check if computer can win.
        $move = findWin($bd, COMPUTER);
        
        // Check if computer can block.
        if (($mode >= 2) && ($move == 0))
            $move = findWin($bd, HUMAN);
        
        if ($mode == 3) {
            // Go to center if Human goes first.
            if (($move == 0) && ($bd[0] == 1 ))
                $move = getCenter($bd);
            
            // Block corner trap.
            if (($move == 0) && oppCorners($bd))
                $move = getEdge($bd);
            
            // Go to corner.
            if ($move == 0)
                $move = getCorner($bd);
        }

        // Pick any remaining cell, if no other moves were possible.
        if ($move == 0)
            $move = nextAvail($bd);
        
        return $move;
    }

    
    // Start program here.
    
    // Initialize the program variables for initial run.
    if (!isset($player)) {
        $player = HUMAN;
        $mode = 0;
        $state = 0;
    }
    
    $mesg = "X = Human, O = Computer";

    // Process user command based on key pressed.
    switch ($cmd) {
        case 1 : // Easy mode.
            $mode = 1;
            break;
            
        case 2 : // Medium mode.
            $mode = 2;
            break;
            
        case 3 : // Hard mode.
            $mode = 3;
            break;
            
        case 4 : // Perform human move.
            $state = 2;
            break;
            
        case 5 : // Reset game.
            $state = 0;
            break;
            
        case 6 : // Display the game board.
            $state = 1;
            break;
    }

    
    // Use state machine to control program execution and phone screen display.
    while (TRUE) {
        switch($state) {
            case 0 : // Reset the game.
                list($bd) = initBoard($bd);
        
                if ($player == HUMAN)
                    $state = 1;
                else
                    $state = 3;
                break;
        
            case 1 : // Display the game board state.
                break 2;
            
            case 2 : // Perform the human move.
                $player = HUMAN;
                $move = humanMove($bd, $move);
                
                // If the board location is not valid, retry.
                if ($move == 0) {
                    $state = 2;
                    $mesg = "Enter next move location.";
                    break 2;
                }

                // Record the move on the board and change player.
                $bd[$move] = $player;
                $bd[0]++;
            
                // Did the human win or tie?
                if (win($bd, $player) || ($bd[0] == 9))
                    $state = 4;
                else
                    $state = 3;
        
                break;
            
            case 3 : // Perform the computer move.
                $player = COMPUTER;
                $move = computerMove($bd, $mode);
                $mesg = "Computer moved to ".$move.".";
            
                // Record the move and update move count.
                $bd[$move] = $player;
                $bd[0]++;
            
                // Did the computer win or tie?
                if (win($bd, $player) || ($bd[0] == 9))
                    $state = 4;
                else {
                    $state = 1;
                    break 2;
                }
        
                break;
        
            case 4 : // Display the game results and determine who starts next game.
                switch(win($bd, $player)) {
                    case -1 :
                        $mesg = "The computer won!";
                        break;
                        
                    case 0 :
                        $mesg = "Nobody won. A tie!";
                        $player = -$player;
                        break;
                        
                    case 1 : // Display the game board state.
                        $mesg = "You won the game!";
                }
                
                break 2;
        }
    }

        
    // Update IP Phone display.
    switch($state) {
        case 1 : // Display the game board state.
        case 4 :
            echo "<CiscoIPPhoneText>\n";
            echo "  <Title>Tic Tac Toe</Title>\n";
            echo "  <Text>";
            printf("\t\t\t\t%s\t\t%s\t\t%s\n", token($bd[1]), token($bd[2]), token($bd[3]));
            printf("\t\t\t\t%s\t\t%s\t\t%s\n", token($bd[4]), token($bd[5]), token($bd[6]));
            printf("\t\t\t\t%s\t\t%s\t\t%s\n", token($bd[7]), token($bd[8]), token($bd[9]));
            echo "  </Text>\n";

            if ($mode == 0) {
                echo "  <SoftKeyItem>\n";
                echo "	<Name>Easy</Name>\n";
                echo "	<URL>http://192.168.3.250/cisco/tictac.php?key=1</URL>\n";
                echo "	<Position>1</Position>\n";
                echo "  </SoftKeyItem>\n";
                
                echo "  <SoftKeyItem>\n";
                echo "	<Name>Medium</Name>\n";
                echo "	<URL>http://192.168.3.250/cisco/tictac.php?key=2</URL>\n";
                echo "	<Position>2</Position>\n";
                echo "  </SoftKeyItem>\n";
                
                echo "  <SoftKeyItem>\n";
                echo "	<Name>Hard</Name>\n";
                echo "	<URL>http://192.168.3.250/cisco/tictac.php?key=3</URL>\n";
                echo "	<Position>3</Position>\n";
                echo "  </SoftKeyItem>\n";
            }
            
            elseif ($state == 1) {
                echo "  <SoftKeyItem>\n";
                echo "	<Name>Move</Name>\n";
                echo "	<URL>http://192.168.3.250/cisco/tictac.php?key=4</URL>\n";
                echo "	<Position>2</Position>\n";
                echo "  </SoftKeyItem>\n";
            }
        
            elseif ($state == 4) {
                echo "  <SoftKeyItem>\n";
                echo "	<Name>Next</Name>\n";
                echo "	<URL>http://192.168.3.250/cisco/tictac.php?key=5</URL>\n";
                echo "	<Position>2</Position>\n";
                echo "  </SoftKeyItem>\n";
            }

            echo "  <SoftKeyItem>\n";
            echo "    <Name>Exit</Name>\n";
            echo "    <URL>SoftKey:Exit</URL>\n";
            echo "    <Position>4</Position>\n";
            echo "  </SoftKeyItem>\n";

            echo "  <Prompt>".$mesg."</Prompt>\n";
            echo "</CiscoIPPhoneText>\n";
            break;

        case 2 : // Perform the human move.
            echo "<CiscoIPPhoneInput>\n";
            echo "  <Title>Tic Tac Toe</Title>\n";
            echo "  <URL method=\"post\">http://192.168.3.250/cisco/tictac.php</URL>\n";
            
            echo "  <InputItem>\n";
            echo "    <DisplayName>Position</DisplayName>\n";
            echo "    <QueryStringParam>move</QueryStringParam>\n";
            echo "    <DefaultValue> </DefaultValue>\n";
            echo "    <InputFlags>N</InputFlags>\n";
            echo "  </InputItem>\n";
            
            echo "  <SoftKeyItem>\n";
            echo "	<Name>Update</Name>\n";
            echo "	<URL>SoftKey:Submit</URL>\n";
            echo "	<Position>2</Position>\n";
            echo "  </SoftKeyItem>\n";
            
            echo "  <SoftKeyItem>\n";
            echo "	<Name>Cancel</Name>\n";
            echo "	<URL>http://192.168.3.250/cisco/tictac.php?key=6</URL>\n";
            echo "	<Position>4</Position>\n";
            echo "  </SoftKeyItem>\n";
            
            echo "  <Prompt>$mesg</Prompt>\n";
            echo "</CiscoIPPhoneInput>\n";
            break;
    }
    
    
    // Save current variable state.
    $_SESSION['id'] = "tictac";
    $_SESSION['bd'] = $bd;          # Game board.
    $_SESSION['move'] = $move;      # Human player move.
    $_SESSION['player'] = $player;  # Last player.
    $_SESSION['state'] = $state;    # Game state.
    $_SESSION['mode'] = $mode;      # Game difficulty.
?>
