<?php /* TICKETSMITH $Id: comment.php,v 1.18 2004/11/18 14:41:57 gregorerhardt Exp $ */

if (!$canEdit) {
	$AppUI->redirect( "m=public&a=access_denied" );
}

$ticket = dPgetParam( $_GET, 'ticket', '' );
$ticket_type = dPgetParam( $_GET, 'ticket_type', '' );

// setup the title block
$titleBlock = new CTitleBlock( 'Post Comment', 'gconf-app-icon.png', $m, "$m.$a" );
$titleBlock->addCrumb( "?m=ticketsmith", "tickets list" );
$titleBlock->addCrumb( "?m=ticketsmith&a=view&ticket=$ticket", "view this ticket" );
$titleBlock->show();

require("modules/ticketsmith/config.inc.php");
require("modules/ticketsmith/common.inc.php");

/* set title */
$title = "Post Comment";

/* prepare ticket parent */
if (!$ticket_parent) {
    $ticket_parent = $ticket;
}

$author_name = dPgetParam( $_POST, 'author_name', '' );
$author_email = dPgetParam( $_POST, 'author_email', '' );
$comment = dPgetParam( $_POST, 'comment', '' );
$body = dPgetParam( $_POST, 'body', '' );

if (@$comment) {

    /* prepare fields */
    list($author_name, $author_email) = query2array("SELECT CONCAT_WS(' ',contact_first_name,contact_last_name) as name, contact_email as email FROM users u LEFT JOIN contacts ON u.user_contact = contact_id WHERE user_id = '$AppUI->user_id'");
    $subject = db_escape( query2result("SELECT subject FROM tickets WHERE ticket = '$ticket_parent'") );
    $comment = db_escape( $comment );
    $author = $author_name . " <" . $author_email . ">";
    $timestamp = time();
    $body = escape_string($body);

    /* prepare query */
    $query = "INSERT INTO tickets (author, subject, body, timestamp, type, parent, assignment) ";
    $query .= "VALUES ('$author','$subject','$comment','$timestamp','Staff Comment','$ticket_parent','9999')";

    /* insert comment */
    do_query($query);

    /* update parent ticket's timestamp */
    do_query("UPDATE tickets SET activity = '$timestamp' WHERE ticket = '$ticket_parent'");

    /* return to ticket view */
    echo("<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=index.php?m=ticketsmith&a=view&ticket=$ticket_parent\">");

    exit();

} else {

    /* start table */
	print("<table class=std bgcolor=\"#eeeeee\" width=\"100%\">\n");
    print("<tr>\n");
	print("<th colspan=\"2\" align=\"center\" >\n");
    print("<div class=\"heading\">".$AppUI->_($title)."</div>\n");
    print("</th>\n");
    print("</tr>\n");

    /* start form */
    print("<form name='ticketform' action=\"index.php?m=ticketsmith&a=comment&ticket=$ticket\" method=\"post\">\n");

    /* determine poster */
    print("<tr>\n");
    print("<td align=\"left\"><strong>".$AppUI->_('From')."</strong></td>");
    list($author_name, $author_email) = query2array("SELECT CONCAT_WS(' ',contact_first_name,contact_last_name) as name, contact_email as email FROM users u LEFT JOIN contacts ON u.user_contact = contact_id WHERE user_id = '$AppUI->user_id'");
    print("<td align=\"left\">" . $author_name . " &lt;" . $author_email . "&gt;</td>\n");
    print("</tr>");

    /* output textarea */
    print("<tr>\n");
    print("<td align=\"left\"><br /></td>");
    print("<td align=\"left\">");
    print("<tt>\n");
    print("<textarea name=\"comment\" wrap=\"hard\" cols=\"72\" rows=\"20\">\n");
    print("</textarea>\n");
    print("</tt>\n");
    print("</td>\n");

    /* output submit button */
    print('<tr><td><br /></td><td><font size=\"-1\"><input type="submit" class=button value="'.$AppUI->_('Post Comment').'"></font></td></tr>');

    /* footer links */
    print("<tr>\n");
    print("<td><br /></td>");
    print("<td>&nbsp;</td>");
    print("</tr>\n");

    /* end table */
    print("</table>\n");

    /* end form */
    print("</form>\n");
}

?>
