<?php

/**
 * This template should only contain the contents of the body
 * of the email, what would be inside of the body tags, and not
 * the header.  You should use tables for layout since Microsoft
 * actually regressed Outlook 2007 to not supporting CSS layout.
 * All styles should be inline.
 *
 * For more information, consult this page:
 * http://www.anandgraves.com/html-email-guide#effective_layout
 *
 * If you are upgrading from an old version of Forward, be sure
 * to visit the Forward settings page to enable use of the new
 * template system.
 */

?>
<html>
  <body>
    <table width="<?php print $width; ?>" cellspacing="0" cellpadding="5" border="0">
      <thead>
        <tr>
          <td>
            <h1 style="font-family:Arial,Helvetica,sans-serif; font-size:18px;">
					<a href="<?php print $site_url; ?>" title="<?php print $site_name; ?>">
						<img  src='<?php echo $base_url ?>/sites/all/themes/themex/images/logo-email.png' alt='ALE Property Group'/>
					</a>
				</h1>
          </td>
        </tr>
      </thead>
      <tbody>
				<tr>
          <td style="font-family:Arial,Helvetica,sans-serif; font-size:12px; line-height:22px;">
						<p><?php echo getTermDescription('Email Greeting') ?><br/><br/>
						<?php preg_match("/<a[^>]*>(.*?)<\\/a>/si", $vars['title'], $match); $content = $match[1]; ?>
						<?php if ($_SESSION['forward']=='compendium'): ?>					
						<?php								
							$link=str_replace('>'.$content.'</a>',' style="color:#00aeef;">'.getTermDescription('Compendium Email Link Text').'</a>',$vars['title']);
							echo str_replace(array('[link]','[sender]'),array($link,$variables['vars']['name']),getTermDescription('Compendium Email Body'));				
						?>
					</p>
					
				 <?php else: ?>
						<?php								
							$link=str_replace('>'.$content.'</a>',' style="color:#00aeef;">'.getTermDescription('Property Email Link Text').'</a>',$vars['title']);
							echo str_replace(array('[link]','[sender]'),array($link,$variables['vars']['name']),getTermDescription('Property Email Body'));				
						?>
				 <?php endif ?>
					<?php if ($message): ?>
						<p><?php print t('Message from Sender:'); ?></p><p><?php print $message; ?></p>
					<?php endif ?>
					
				</td>
      </tr>

      </tbody>
    </table>
  </body>
</html>