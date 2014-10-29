/**
 * ProcessWire Admin Theme jQuery/Javascript
 *
 * Copyright 2012 by Ryan Cramer
 * 
 */

var ProcessWireAdminTheme = {

	/**
	 * Initialize the default ProcessWire admin theme
	 *
	 */
	init: function() {
		// fix annoying fouc with this particular button
		var $button = $("#head_button > button.dropdown-toggle").hide();

		this.setupCloneButton();
		// this.setupButtonStates();
		this.setupFieldFocus();
		this.setupTooltips();
		this.setupSearch();
		this.setupDropdowns();
		this.setupMobile();
		// this.sizeTitle();
		var $body = $("body");
		if($body.hasClass('hasWireTabs') && $("ul.WireTabs").size() == 0) $body.removeClass('hasWireTabs'); 
		$('#content').removeClass('fouc_fix'); // FOUC fix, deprecated
		$body.removeClass("pw-init").addClass("pw-ready"); 
		
		this.browserCheck();

		if($button.size() > 0) $button.show();
	},

	/**
	 * Enable jQuery UI tooltips
	 *
	 */
	setupTooltips: function() {
		$("a.tooltip").tooltip({ 
			position: {
				my: "center bottom", // bottom-20
				at: "center top"
			}
		}).hover(function() {
			$(this).addClass('ui-state-hover');
		}, function() {
			$(this).removeClass('ui-state-hover');
		}); 
	},

	/**
	 * Clone a button at the bottom to the top 
	 *
	 */
	setupCloneButton: function() {
		// no head_button in modal view
		if($("body").is(".modal")) return;

		// if there are buttons in the format "a button" without ID attributes, copy them into the masthead
		// or buttons in the format button.head_button_clone with an ID attribute.
		var $buttons = $("#content a[id=''] button[id=''], #content button.head_button_clone[id!='']"); 
		//var $buttons = $("#content a:not([id]) button:not([id]), #content button.head_button_clone[id!=]"); 

		// don't continue if no buttons here or if we're in IE
		if($buttons.size() == 0 || $.browser.msie) return;

		var $head = $("<div id='head_button'></div>").prependTo("#breadcrumbs .container").show();
		$buttons.each(function() {
			var $t = $(this);
			var $a = $t.parent('a'); 
			if($a.size()) { 
				$button = $t.parent('a').clone();
				$head.append($button);
			} else if($t.is('.head_button_clone')) {
				$button = $t.clone();
				$button.attr('data-from_id', $t.attr('id')).attr('id', $t.attr('id') + '_copy');
				$a = $("<a></a>").attr('href', '#');
				$button.click(function() {
					$("#" + $(this).attr('data-from_id')).click(); // .parents('form').submit();
					return false;
				});
				$head.append($a.append($button));	
			}
		}); 
	},

	/**
	 * Make buttons utilize the jQuery button state classes
	 *	
 	 */
	setupButtonStates: function() {
		// jQuery UI button states
		$(".ui-button").hover(function() {
			$(this).removeClass("ui-state-default").addClass("ui-state-hover");
		}, function() {
			$(this).removeClass("ui-state-hover").addClass("ui-state-default");
		}).click(function() {
			$(this).removeClass("ui-state-default").addClass("ui-state-active"); // .effect('highlight', {}, 100); 
		});

		// make buttons with <a> tags click to the href of the <a>
		$("a > button").click(function() {
			window.location = $(this).parent("a").attr('href'); 
		}); 
	},

	/**
	 * Make the first field in any forum have focus, if it is a text field
	 *
	 */
	setupFieldFocus: function() {
		// add focus to the first text input, where applicable
		jQuery('#content input[type=text]:visible:enabled:first:not(.hasDatepicker)').each(function() {
			var $t = $(this); 
			if(!$t.val() && !$t.is(".no_focus")) window.setTimeout(function() { $t.focus(); }, 1);
		});

	},


	/**
	 * Make the site search use autocomplete
	 * 
	 */
	setupSearch: function() {

		$.widget( "custom.adminsearchautocomplete", $.ui.autocomplete, {
			_renderMenu: function(ul, items) {
				var that = this;
				var currentType = "";
				$.each(items, function(index, item) {
					if (item.type != currentType) {
						ul.append("<li><a>" + item.type + "</a></li>" );
						currentType = item.type;
					}
					ul.attr('id', 'ProcessPageSearchAutocomplete'); 
					that._renderItemData(ul, item);
				});

				ul.detach().appendTo('#ProcessPageSearchForm'); // move for better styling and easier positioning
			},
			_renderItemData: function(ul, item) {
				if(item.label == item.template) item.template = '';
				ul.append("<li><a href='" + item.edit_url + "'>" + item.label + " <small>" + item.template + "</small></a></li>"); 
			}
		});
		
		var $input = $("#ProcessPageSearchQuery"); 
		var $status = $("#ProcessPageSearchStatus"); 
		
		$input.adminsearchautocomplete({
			minLength: 2,
			search: function(event, ui) {
				$status.html("<img src='" + config.urls.modules + "Process/ProcessPageList/images/loading.gif'>");
			},
			source: function(request, response) {
				var url = $input.parents('form').attr('data-action') + 'for?get=template_label,title&include=all&admin_search=' + request.term;
				$.getJSON(url, function(data) {
					var len = data.matches.length; 
					if(len < data.total) $status.text(data.matches.length + '/' + data.total); 
						else $status.text(len); 
					response($.map(data.matches, function(item) {
						return {
							label: item.title,
							value: item.title,
							page_id: item.id,
							template: item.template_label ? item.template_label : '',
							edit_url: item.editUrl,
							type: item.type
						}
					}));
				});
			},
			select: function(event, ui) { }
		}).blur(function() {
			$status.text('');	
		});
		
	},

	// whether or not dropdown positions are currently being monitored
	dropdownPositionsMonitored: false,

	setupDropdowns: function() {

		var $dropdownToggles = $('.dropdown-toggle');
		var $dropdownMenus = $('.menu');

		$dropdownMenus.each(function() {

			var $ul = $(this);
			var $a = $ul.siblings('a').children(".dropdown-toggle"); 

			function closeDropdowns(){
				$dropdownToggles.removeClass('on');
				$dropdownMenus.removeClass('is-open');
			}

			if($a.is("button")) {
				$a.button();
			}

			$ul.find(".has-items").each(function() {
				var $icon = $("<i class='has-items-icon fa fa-caret-down'></i>");
				$(this).prepend($icon);
			}); 

			$a.click(function(event) {
				event.preventDefault();

				var $this = $(this);
				if ($this.hasClass('on')) {
					
					$this.removeClass('on');
					$ul.find('ul').removeClass('is-open');
					$ul.removeClass('is-open');

				}
				else{

					closeDropdowns();

					$this.addClass('on');
					if(!$ul.hasClass('dropdown-ready')) {
						// $ul.prependTo($('#dropdowns')).addClass('dropdown-ready').menu();
					}
					$ul.addClass("is-open");
				}

			})

			$ul.mouseleave(function() {
				if($a.is(":hover")) return;

				closeDropdowns();
			}); 

		});

		
		// ajax loading of fields and templates
		$(document).on('click', 'ul.menu a.has-ajax-items:not(.ajax-items-loaded) i.has-items-icon', function(event) {

			event.preventDefault();

			var $a = $(this).parent("a"); 
		
			if (!$a.hasClass('ajax-items-loaded')) {

				 	
				var url = $a.attr('data-json');
				var $ul = $a.siblings('ul').addClass('menu'); 
				var $itemsIcon =  $a.children('.has-items-icon');
				$itemsIcon.addClass('loading'); 

				$.getJSON(url, function(data) {
					$itemsIcon.removeClass('loading fa-caret-down').addClass('fa-caret-up'); 

					if(data.add) {				
						var $li = $("<li class='menu-item add'><a href='" + data.url + data.add.url + "'><i class='fa fa-fw fa-plus'></i>" + data.add.label + "</a></li>");
						$ul.append($li);
					}
					// populate the retrieved items
					$.each(data.list, function(n) {
						var icon = '';
						if(this.icon) icon = "<i class='ui-priority-secondary fa fa-fw fa-" + this.icon + "'></i>";
						var $li = $("<li class='menu-item'><a href='" + data.url + this.url + "'>" + icon + this.label + "</a></li>");
						$ul.append($li);
					}); 
					
					$ul.addClass('navJSON length' + parseInt(data.list.length)); 

					$ul.find("a").click(function() {
						// prevent a clicked link from jumping back to the top of page (makes the UI nicer)
						window.location.href = $(this).attr('href');
						return false; 
					}); 
					
					

				}).done(function() {
				    $a.addClass('ajax-items-loaded'); // add confirmation after success state
				}); // getJSON

			}


			if ($a.hasClass('on')) {
				$a.siblings(".menu").removeClass('is-open');
			}
			else{
				$a.addClass('on');
				$a.siblings(".menu").addClass('is-open');
			}


				

			
		});


	}, 	

	setupMobile: function() {
		// collapse or expand the topnav menu according to whether it is wrapping to multiple lines
		var collapsedTopnavAtBodyWidth = 0;
		var collapsedTabsAtBodyWidth = 0;

		var windowResize = function() {

			// top navigation
			var $topnav = $("#topnav"); 
			var $body = $("body"); 
			var height = $topnav.height();

			if(height > 50) {
				// topnav has wordwrapped
				if(!$body.hasClass('collapse-topnav')) {
					$body.addClass('collapse-topnav'); 
					collapsedTopnavAtBodyWidth = $body.width();
				}
			} else if(collapsedTopnavAtBodyWidth > 0) {
				// topnav is on 1 line
				var width = $body.width();
				if($body.hasClass('collapse-topnav') && width > collapsedTopnavAtBodyWidth) {
					$body.removeClass('collapse-topnav'); 
					collapsedTopnavAtBodyWidth = 0;
				}
			}

			$topnav.children('.collapse-topnav-menu').children('a').click(function() {
				if($(this).is(".hover")) {
					// already open? close it. 
					$(this).mouseleave();
				} else {
					// open it again
					$(this).mouseenter();
				}
				return false;
			}); 

			// wiretabs
			var $wiretabs = $(".WireTabs"); 
			if($wiretabs.size < 1) return;

			$wiretabs.each(function() {
				var $tabs = $(this);
				var height = $tabs.height();
				if(height > 65) {
					if(!$body.hasClass('collapse-wiretabs')) {
						$body.addClass('collapse-wiretabs'); 
						collapsedTabsAtBodyWidth = $body.width();
						// console.log('collapse wiretabs'); 
					}
				} else if(collapsedTabsAtBodyWidth > 0) {
					var width = $body.width();
					if($body.hasClass('collapse-wiretabs') && width > collapsedTabsAtBodyWidth) {
						$body.removeClass('collapse-wiretabs'); 
						collapsedTabsAtBodyWidth = 0;
						// console.log('un-collapse wiretabs'); 
					}
				}
			}); 
		};

		windowResize();
		$(window).resize(windowResize);

	}, 

	/**
	 * Give a notice to IE versions we don't support
	 *
	 */
	browserCheck: function() {
		if($.browser.msie && $.browser.version < 8) 
			$("#content .container").html("<h2>ProcessWire does not support IE7 and below at this time. Please try again with a newer browser.</h2>").show();
	}

};

$(document).ready(function() {
	ProcessWireAdminTheme.init();

	$("#notices .notice-remove").click(function() {
		$("#notices").slideUp('fast', function() { $(this).remove(); }); 
	}); 
}); 
