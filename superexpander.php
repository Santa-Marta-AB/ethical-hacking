<?php
/*---------------------------------------------------------------------------------------------------------
 * SCRIPT: SuperExpander - Generates a word list based on variations over a single word
 * Eduardo Valdelomar - Santa Marta AB - July 2022
 *---------------------------------------------------------------------------------------------------------
*/

if($argc < 2)
{
    echo "Use the word as parameter\n"; 
    exit;
}

$word = strtolower($argv[1]);

echo "The base word is ".$word."\n";

//Define symbols
$symbols = array("","#","@","$","?","-","!","¡","¿","_",".");
$comb2symbols =$symbols;
foreach($symbols as $char1)
{
    foreach($symbols as $char2)
    {
        $comb2symbols[] = $char1.$char2;
    }

}
$comb2symbols = array_unique($comb2symbols);

//Define substitutions words by numbers
$num = array("a" => "4", "e" => "3", "i" => "1", "o" => "0", "s" => "5", "b" => "6");

$list = array($word);

//Consider reduced versions of these words up to 3 letters
echo "Reducing letters...\n";

if(strlen($word) > 3)
{
    for($i=strlen($word)-1; $i >= 3; $i--)
    {
        $list[] = substr($word,0,$i);
    }
}

echo "Total: ".sizeof($list)." words\n";

//Substitute letters by numbers
echo "Reinterpreting letters as numbers...\n";

$newList = $list;
foreach($newList as $item)
{
    $list = array_merge($list,subst($item,$num));
}
$newList = array();

echo "Total: ".sizeof($list)." words\n";


//Substitute lower by upper letters
echo "Alternating upper and lower letters...\n";
$newList = $list;
foreach($newList as $item)
{
    $list = array_merge($list,upper($item));
}
$newList = array();
echo "Total: ".sizeof($list)." words\n";

//Add numbers from 0 to 20, with and without leading 0 in numbers of 1 digit
echo "Adding numbers...\n";
$newList = $list;
foreach($newList as $item)
{
    for($i=0; $i <= 20; $i++)
    {
        $list[] = $item.$i;
        $list[] = $i.$item;
        $list[] = $item.".".$i;
        $list[] = $item."-".$i;
        $list[] = $item."_".$i;
        $list[] = $i.".".$item;
        $list[] = $i."-".$item;
        $list[] = $i."_".$item;
    }
    for($i=0; $i < 10; $i++)
    {
        $list[] = $item."0".$i;
        $list[] = "0".$i.$item;
        $list[] = $item.".0".$i;
        $list[] = $item."-0".$i;
        $list[] = $item."_0".$i;
        $list[] = "0".$i.".".$item;
        $list[] = "0".$i."_".$item;
        $list[] = "0".$i."-".$item;
    }
}
echo "Total: ".sizeof($list)." words\n";

//Add years from 1980 to 2021
echo "Adding years from 1980 to 2021...\n";
for($i=1980;$i<=2021;$i++)
{
    foreach($newList as $item)
    {
        $list[] = $item.$i;
        $list[] = $item."-".$i;
        $list[] = $item."_".$i;
        $list[] = $item.".".$i;
        $list[] = $i.$item;
        $list[] = $i."-".$item;
        $list[] = $i."_".$item;
        $list[] = $i.".".$item;
    }

}
$newList = array();
echo "Total: ".sizeof($list)." words\n";


//Adding symbols and saving
$file = fopen("result.txt","w");
$count =0; $partial = 0;
foreach($list as $item)
{
    foreach($symbols as $sym1)
    {
        foreach($comb2symbols as $sym2)
        {
            fputs($file,$sym1.$item.$sym2."\n");
            $count++;
            $partial++; 
            if($partial == 1000000) { echo "Calculating... ".($count / 1000000)." mill. words\n"; $partial = 0; }
        }
    }
}
echo "Total: ".$count." words\n";


//Recursive functions 
function subst($text, $num)
{
    $result = array(); 

    $letter = substr($text,0,1);
    $rest = ""; 

    if(strlen($text) > 1)
    {
        $rest = substr($text,1);
    }

    if($rest == "")
    {
        $comb = array("");
    }
    else
    {
        $comb = subst($rest,$num);
    }

    foreach($comb as $item)
    {
        $result[] = $letter.$item;
    }
    if(isset($num[$letter]))
    {
        foreach($comb as $item)
        {
            $result[] = $num[$letter].$item;
        }
    }
    return $result;
}

function upper($text)
{
    $result = array(); 

    $letter = substr($text,0,1);
    $rest = ""; 

    if(strlen($text) > 1)
    {
        $rest = substr($text,1);
    }

    if($rest == "")
    {
        $comb = array("");
    }
    else
    {
        $comb = upper($rest);
    }

    foreach($comb as $item)
    {
        $result[] = $letter.$item;
    }
    if(strtoupper($letter) != $letter)
    {
        foreach($comb as $item)
        {
            $result[] = strtoupper($letter).$item;
        }
    }
    return $result;
}


?>