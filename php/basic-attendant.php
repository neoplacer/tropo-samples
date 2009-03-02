 <?php
require 'tropoStub.php';

// ===========
// Tropo basic auto attendant sample application
// v1 jrt -> php rsc
// ===========

// -----------
// kludge to quit

function allDone()
{
   throw( "exiting" );
}

// -----------
// turn the contacts into a comma seperated list of names

function listNames( $theContacts )
{
  $s = '';

  foreach( $theContacts as $index => $contact ) {
   if ($s != '') $s .= ", " ;
    $s .= $index;
  }

  return $s;
}  //function listNames

// -----------
// turn the contacts into a comma seperated list of options for each contact (simple grammar)

function listOptions( $theContacts )
{
  $s ='';

  foreach( $theContacts as $index => $contact ) {
    if ($s != '') $s .= ", " ;
    $s .= $index . " (" . $contact['nameChoices'] . ")";
  }

  return $s;
}

// -----------
// start
// -----------

// define the list of contacts

$contacts = array("jonathan"=> array("nameChoices" => "Jonathan, Jonathan Taylor", "number" => "14074434233" ),
			      "michael" => array("nameChoices" => "Michael, Michael Smith",    "number" => "14074181800" ),
				  "stephen" => array("nameChoices" => "Stephen, Stephen Neish",    "number" => "14076463131" ) );


// answer the phone and play the initial greeting

answer( 30 );

prompt( "hello, and thank you for calling." );

// prompt the user for the name of the person they desire
$event = prompt( "Who would you like to call?  Just say " . listNames( $contacts ),
			array (
			  "repeat"  => "3",
			  "timeout" => "7",
			  "choices" => listOptions( $contacts ),
			  "onTimeout" => create_function( '$event', 'say( "I\'m sorry, I didn\'t hear anything." );'),
			  "onBadChoice" => create_function( '$event', 'say( "I\'m sorry, I didn\'t understand what you said." );')
			) 
		 );

// if they made a choice, transfer to that person

if ($event->name == 'choice')
{
  say( "ok, you said " . $event->value . ".  Please hold while I transfer you." );

$ne = transfer( "sip:9" . $contacts[ $event->value ]['number'] . "@10.6.63.201",
	     array (
	     "answerOnMedia" => false,
	     "callerID"      => "tel:+14076179024",
	     "timeout"       => 60.3456,
	     "method"        => "bridged", // fixed to bridged currently
	     "playrepeat"    => 3,
	     "playvalue"     => "Ring... Ring... Ring...",
	     "choices"       => "1,2,3,4,5,6,7,8,9,0,*,#",
	     "onSuccess"     => create_function('$event', '_log("*********transfered to:  " . $event->value->calledId);'),  //~!~ fix
	     "onError"       => create_function('$event', '_log("*********transfer error");'),
	     "onTimeout"     => create_function('$event', '_log("*********transfer timeout");'),
	     "onCallFailure" => create_function('$event', '_log("*********transfer failed");'),
	     "onChoice"      => create_function('$event', '_log("*********transfer canceled");')
	     ) 
      );

  _log( "transfer event.name  = " . $ne->name );
  _log( "transfer event.value = " . $ne->value );
  say( "The other party disconnected.  Goodbye" );

}

hangup();
?>