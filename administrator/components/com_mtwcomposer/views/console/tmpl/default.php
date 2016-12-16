<?php
/**
* mtwComposer
*
* @version $Id$
* @package Matware
* @subpackage mtwComposer
* @copyright Copyright 2004 - 2016 Matias Aguirre. All rights reserved.
* @license GNU General Public License version 2 or later.
* @author Matias Aguirre <maguirre@matware.com.ar>
* @link http://www.matware.com.ar
*/

defined('_JEXEC') or die;

$user	= \JFactory::getUser();
$userId	= $user->get('id');
?>

<script src="<?php echo JUri::root(); ?>media/com_mtwcomposer/js/jquery.terminal-0.11.21.min.js"></script>
<link href="<?php echo JUri::root(); ?>media/com_mtwcomposer/css/jquery.terminal.css" rel="stylesheet"/>

<section class="content">
	<div class="container">
		<div class="row">
				<div class="col-md-1"></div>
				<div class="col-md-10">
					<!--<div class="meter red">
						<span style="width: 100%"></span>
					</div>-->
					<div id="progress" class="progress progress-info progress-striped">
			        <div id="progresstext" class="bar" style="width: 100%;"><?php echo JText::_('COM_MTWCOMPOSER_WAITING'); ?></div>
			    </div>
					<div id="mtwcomposerdiv" class="terminal">
					</div>
				</div>
				<div class="col-md-1"></div>
		</div>
	</div>
</section>

<script type="text/javascript">
	jQuery(function($, undefined) {

		var url1 = 'index.php?option=com_mtwcomposer&format=raw&task=ajax.status';
		var url2 = 'index.php?option=com_mtwcomposer&format=raw&task=ajax.command';
		var url3 = 'index.php?option=com_mtwcomposer&format=raw&task=ajax.downloadComposer';

		$('#mtwcomposerdiv').terminal(function(command, term) {

				if (command == 'status' || command.substring(0, 9) == 'composer ') {

					term.pause();
					$('#progress').addClass('active');
					$('#progress').addClass('progress-success');
					$('#progress').removeClass('progress-info');
					$('#progresstext').html('<?php echo JText::_('COM_MTWCOMPOSER_EXECUTING'); ?>');

					if (command == 'status')
					{
						$.get(url1,	function(result) {
							//console.log('--------------');
							//console.log(result);

							var result = $.param(result)

							if (result !== undefined) {
								term.resume();
								term.echo(new String(result));
							}
						});
					}

					if (command.substring(0, 9) == 'composer ')
					{
						$.post(url2, {
                "command": command,
                "type": "command"
              },	function(result) {
								//console.log('--------------');
								//console.log(result);

								try
								{
									if (result !== undefined) {
										term.resume();
										$('#progress').removeClass('active');
										$('#progress').removeClass('progress-success');
										$('#progress').addClass('progress-info');
										$('#progresstext').html('<?php echo JText::_('COM_MTWCOMPOSER_WAITING'); ?>');

										term.echo(new String(result));
									}
								} catch(e) {
										term.error(new String(e));
								}
						});
					}

				} else {
					 term.echo('<?php echo JText::_('COM_MTWCOMPOSER_COMMAND_NOT_FOUND'); ?>');
				}
		}, {
			greetings: '       _         _____                           v1.0\n _____| |_ _ _ _|     |___ _____ ___ ___ ___ ___ ___ \n|     |  _| | | |   --| . |     | . | . |_ -| -_|  _|\n|_|_|_|_| |_____|_____|___|_|_|_|  _|___|___|___|_|  \n                                |_|                  \n\nUsage: composer [command] [arguments]\n\n',
			name: 'mtwcomposerdiv',
			height: 600,
			prompt: '|mtwComposer > '
		});
	});
</script>
