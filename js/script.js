jQuery.extend({
	isMobile: function() {
		return navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/);
	}
});

jQuery.fn.extend({
	insertAtCaret: function(myValue) {
		return this.each(function() {
			var me = this;
			if (document.selection) { // IE
				me.focus();
				sel = document.selection.createRange();
				sel.text = myValue;
				me.focus();
			}
			else if (me.selectionStart || me.selectionStart == '0') { // Real browsers
				var startPos = me.selectionStart, endPos = me.selectionEnd, scrollTop = me.scrollTop;
				me.value = me.value.substring(0, startPos) + myValue + me.value.substring(endPos, me.value.length);
				me.focus();
				me.selectionStart = startPos + myValue.length;
				me.selectionEnd = startPos + myValue.length;
				me.scrollTop = scrollTop;
			}
			else {
				me.value += myValue;
				me.focus();
			}
		});
	}
});

var UI = {};
UI.PopupView = (function() {
	function PopupView(default_parent) {
		this.default_parent = default_parent;
		this._popupStack = [];
		this._popupArea = this.default_parent.querySelector(".popup_area");
		this._popupStyle = null;
		this._popupMarginHeight = -1;
		this._currentX = 0;
		this._currentY = 0;
		this._waitMs = $.isMobile() ? 0 : 50;
		this._first = true;
		return;
	}
	
	PopupView.prototype._on_click = function(e) {
		var id = -1;
		for(var i=0; i<this._popupStack.length; i++) {
			console.log(this._popupStack[i]);
			var top = $(this._popupStack[i].popup).position().top;
			var bottom = top + $(this._popupStack[i].popup).height();
			if( top<=e.clientY && e.clientY<=bottom ) id = i;
		}
		
		if( id==-1 ) {
			this._remove(true);
		}
	};
	
	PopupView.prototype._on_mousemove = function(e) {
		this._currentX = e.clientX;
		this._currentY = e.clientY;	
	};

	/**
	@method show
	@param {Element} popup
	@param {Number} mouseX
	@param {Number} mouseY
	@param {Element} source
	*/
	PopupView.prototype.show = function(popup, mouseX, mouseY, source) {
		var popupInfo;
		var isMobile = $.isMobile();
		this.popup = popup;
		this.mouseX = mouseX;
		this.mouseY = mouseY;
		this.source = source;
		
		if (this._popupStack.length > 0) {
			popupInfo = this._popupStack[this._popupStack.length - 1];
			if (Object.is(this.source, popupInfo.source)) {
				return;
			}
		}
		
		if (this.source.closest(".popup")) {
			this.source.closest(".popup").classList.add("active");
			this._remove(false);
		} else {
			this._remove(true);
		}
		
		//rendering
		var windowHeight = $(window).innerHeight();
		var space = {
			left: this.mouseX,
			right: $(window).innerWidth() - this.mouseX,
			top: this.mouseY,
			bottom: windowHeight - this.mouseY
		};
		if(isMobile) {
			this.popup.style.left = '0';
			this.popup.style.top = '0';
			this.popup.style.right = '0';
			this.popup.style.width = '100%';
			var outerHeight = this._getOuterHeight(this.popup);
			var spaceUp = this.mouseY - 10;
			if( outerHeight <= spaceUp )
				this.popup.style.top = (this.mouseY - 10 - outerHeight) + 'px';
			this.popup.style.maxHeight = '85%';
		} else {
			var margin = 10;
			if( space.right >= space.left || space.right > 400 ) {
				this.popup.style.left = (space.left + margin) + "px";
				this.popup.style.maxWidth = (space.right - margin * 2) + "px";
			} else {
				this.popup.style.right = (space.right + margin) + "px";
				this.popup.style.maxWidth = (space.left - margin * 2) + "px";
			}
			var outerHeight = this._getOuterHeight(this.popup);
			var top = Math.max(this.mouseY-margin, 0);
			if( space.bottom < 2*margin + outerHeight )
				top = Math.max(0, windowHeight - 2*margin - outerHeight);
			this.popup.style.top = top + "px";
			this.popup.style.maxHeight = (windowHeight - top - margin) + "px";
		}

		if (this._first) {
			this._first = false;
			this._currentX = this.mouseX;
			this._currentY = this.mouseY;
			this.default_parent.addEventListener("mousemove", (function(_this) {
				return function(e) {
					return _this._on_mousemove(e);
				};
			})(this));
			if(isMobile) {
				this.default_parent.addEventListener("click", (function(_this) {
					return function(e) {
						return _this._on_click(e);
					};
				})(this));
			}
		}
		
		this.source.classList.add("popup_source");
		this.source.setAttribute("stack-index", this._popupStack.length);
		this.popup.classList.add("popup");
		this.popup.setAttribute("stack-index", this._popupStack.length);
		this.popup.style.zIndex = (this._popupStack.length + 5).toString();
		
		if(!isMobile) {
			this.popup.addEventListener("mouseenter", (function(_this) {
				return function(e) {
					return _this._on_mouseenter(e.currentTarget);
				};
			})(this));
			this.popup.addEventListener("mouseleave", (function(_this) {
				return function(e) {
					return _this._on_mouseleave(e.currentTarget);
				};
			})(this));
			this.source.addEventListener("mouseenter", (function(_this) {
				return function(e) {
					return _this._on_mouseenter(e.currentTarget);
				};
			})(this));
			this.source.addEventListener("mouseleave", (function(_this) {
				return function(e) {
					return _this._on_mouseleave(e.currentTarget);
				};
			})(this));
		}
		popupInfo = {
			source: this.source,
			popup: this.popup
		};
		this._popupStack.push(popupInfo);
		this._popupArea.appendChild(popupInfo.popup);
		this._activateNode();
		/*setTimeout((function(_this) {
			return function() {
				return _this._activateNode();
			};
		})(this), 0);*/
	};

	/**
	@method _remove
	@param {Boolean} forceRemove
	*/
	PopupView.prototype._remove = function(forceRemove) {
		var popupInfo;
		while (this._popupStack.length > 0) {
			popupInfo = this._popupStack[this._popupStack.length - 1];
			if (forceRemove === false && (popupInfo.source.classList.contains("active") || popupInfo.popup.classList.contains("active"))) {
				break;
			}
			popupInfo.source.classList.remove("popup_source");
			popupInfo.source.removeAttribute("stack-index");
			this._popupArea.removeChild(popupInfo.popup);
			this._popupStack.pop();
		}
	};

	/**
	@method _on_mouseenter
	@param {Object} target
	*/
	PopupView.prototype._on_mouseenter = function(target) {
		var stackIndex;
		target.classList.add("active");
		stackIndex = target.getAttribute("stack-index");
		if (target.classList.contains("popup")) {
			this._popupStack[stackIndex].source.classList.remove("active");
		} else if (target.classList.contains("popup_source")) {
			this._popupStack[stackIndex].popup.classList.remove("active");
		}
		if (this._popupStack.length - 1 > stackIndex) {
			this._popupStack[this._popupStack.length - 1].source.classList.remove("active");
			this._popupStack[this._popupStack.length - 1].popup.classList.remove("active");
			setTimeout((function(_this) {
				return function() {
					return _this._remove(false);
				};
			})(this), this._waitMs);
		}
	};

	/**
	@method _on_mouseleave
	@param {Object} target
	 */
	PopupView.prototype._on_mouseleave = function(target) {
		target.classList.remove("active");
		setTimeout((function(_this) {
			return function() {
				return _this._remove(false);
			};
		})(this), this._waitMs);
	};

	/**
	@method _activateNode
	*/
	PopupView.prototype._activateNode = function() {
		var elm;
		elm = document.elementFromPoint(this._currentX, this._currentY);
		if (Object.is(elm, this.source)) {
			this.source.classList.add("active");
		} else if (Object.is(elm, this.popup) || Object.is(elm.closest(".popup"), this.popup)) {
			this.popup.classList.add("active");
		} else if (elm.classList.contains("popup_source") || elm.classList.contains("popup")) {
			elm.classList.add("active");
		} else if (elm.closest(".popup")) {
			elm.closest(".popup").classList.add("active");
		} else {
			this._popupStack[this._popupStack.length - 1].source.classList.remove("active");
			this._popupStack[this._popupStack.length - 1].popup.classList.remove("active");
			setTimeout((function(_this) {
				return function() {
					return _this._remove(false);
				};
			})(this), this._waitMs);
		}
	};

	/**
	@method _getOuterHeight
	@param {Object} elm
	@param {Boolean} margin
	*/
	PopupView.prototype._getOuterHeight = function(elm) {
		var tmp = elm.style.top;
		var re = 0;
		var cls = elm.className;
		//test height
		elm.style.top = '0';
		$(elm).addClass('popup');
		this._popupArea.appendChild(elm);
		re = elm.clientHeight;
		this._popupArea.removeChild(elm);
		//recover
		elm.style.top = tmp;
		elm.className = cls;
		return re;
	};
	return PopupView;
})();

//pixmicat default functions
function hideform() {
	$("#postform")[0].className = 'hide_btn';
	$("#postform_tbl")[0].className = 'hide';
	$("#hide")[0].className = 'hide';
	$("#show")[0].className = 'show';
}
(function(){
	var uuid = '';
	var idu = 'xxxxxxxx-xxxx-4xxx-xxxx-xxxxxxxxxxxx';
	function checkUUID(x) {
		if(x.length != idu.length+3) return false;
		var sum = 0;
		var n = idu.length;
		var valid = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f'];
		for(var i=0; i<n+3; i++) {
			if( i<n && idu[i]!='x' && x[i]!=idu[i] ) return false;
			if( i<n && idu[i]=='x' && valid.indexOf(x[i]) == -1 ) return false;
			if( i>=n && valid.indexOf(x[i]) == -1 ) return false;
			
			if( i<n ) sum = ( sum*121 + x.charCodeAt(i) ) % 4096;
		}
		sum = (sum).toString(16);
		return sum[0]==x[n] && sum[1]==x[n+1] && sum[2]==x[n+2];
	}
	function generateUUID() {
		var d = new Date().getTime();
		var uuid = idu.replace(/[xy]/g, function(c) {
			var r = (d + Math.random() * 16) % 16 | 0;
			d = Math.floor(d / 16);
			return (r).toString(16);
		});
		
		var x = 0;
		for(var i=0; i<uuid.length; i++) {
			x = x*121 + uuid.charCodeAt(i);
			x %= 4096;
		}
		uuid += (x).toString(16);
		return uuid;
	}
	uuid = localStorage.getItem('pugid');
	if(!uuid || !checkUUID(uuid)) {
		uuid = generateUUID();
		localStorage.setItem('pugid',uuid);
	}
	//
	$(document).ready(function() {
		$('#postform_main').append('<input type="hidden" name="pugid" class="pugid">');
		$('#postform_main').find('.pugid').attr('value', localStorage.getItem('pugid'));
	});
	
	function showform() {
		$("#postform")[0].className = '';
		$("#postform_tbl")[0].className = '';
		$("#hide")[0].className = 'show';
		$("#show")[0].className = 'hide';
	}
	$(document).ready(function() {
		
		$('#show').mouseover(function(){ showform(); });
		$('#hide').mouseover(function(){ hideform(); });
		
		$('#postform_main').submit(function(e) {
			//pixmicat code
			var a, j, ext_allowed, ext_length;
			if ( !$(this).find('#fupfile').length )
				return true;
			
			a = $(this).find('#fupfile')[0].value;
			if (!a && !$(this).find('#fcom')[0].value) {
				alert(msgs[0]);
				return false;
			}
			if (a) {
				ext_allowed = 0;
				ext_length = ext.length;
				for (j = 0; j < ext_length; j++) {
					if (a.substr(a.length - 3, 3).toUpperCase() == ext[j]) {
						ext_allowed = 1;
						break;
					}
				}
				if (!ext_allowed) {
					alert(msgs[1]);
					return false;
				}
			}
			this.sendbtn.disabled = true;
		});
	});
})();

(function(){
	
	// idIndex[op][ID][no]
	var idIndex = {};
	// idCount[op][ID] = count
	var idCount = {};
	// quoteIndex[no][quote_by_no]
	var quoteIndex = {};
	// threadNode[op]
	var threadNode = {};
	// postNode[no]
	var postNode = {};
	var onPostLoadedFn = [];
	var onThreadLoadedFn = [];
	var onThreadUpdatedFn = [];
	
	//UI
	var popupView;
	
	var ajaxTimeout = 10000;
	
	var useRecaptcha = false;
	var recaptchaObject = null;
	
	/*
	 * 隱藏太長文章
	 */
	function hideTooLong(post) {
		if( $('.thread').length == 1 ) return;
		
		$(post).find('.quote').each(function() {
			var SHOW_LINE = 10;
			var ele = $(this)[0].childNodes[0];
			var cnt = 0;
			var flg = 0;
			while(ele) {
				if( cnt<SHOW_LINE ) {
					if( ele.tagName==="BR" || ele.tagName==="DIV" || ele.tagName==="PRE" )
						cnt++;
					ele = ele.nextSibling;
				} else {
					flg = 1;
					var nxt = ele.nextSibling;
					$(ele).wrap('<span class="hide"></span>');
					ele = nxt;
				}
			}
			if(flg) $(this).append('<a href="#" class="-expand-line">展開文章...</a>');
		});
		
		$(post).find('.-expand-line').click(function(e) {
			e.preventDefault();
			$(this).parent().find('.hide').each(function() {
				this.outerHTML = this.innerHTML;
			});
			$(this).remove();
		});
	}
	
	/*
	 * 1. split now, id, ip
	 * 2. add contorl
	 * 3. init index
	 */
	function initPost(post) {
		$(post).find('.now').each(function() {
			var ar = this.innerHTML.split(' ');
			for(var i=ar.length-1; i>=1; i--) {
				if( ar[i][0] == 'I' && ar[i][1]=='D' && ar[i][2]==':' ) {
					var reg = /ID:(\S+)/gi;
					var match = reg.exec( ar[i] );
					var id = match.length>=2 ? match[1] : '';
					$(this).after('<span class="id" data-id="' + id + '">' + ar[i] + '</span>');
				}
				else if( ar[i][0]=='(' )
					$(this).after('<span class="ip">' + ar[i] + '</span>');
				else
					$(this).after('<span>' + ar[i] + '</span>');
			}
			this.innerHTML = ar[0].trim();
		});
		
		$(post).find('.post-head span').not('.qlink, .rlink').click(function() {
			var html = '<menu id="post-menu"></menu>';
			$('#post-menu').remove();
			$('body').append(html);
			
			var delFunc = function(e) {
				$('#post-menu').remove();
				//只發火一次
				document.querySelector('body').removeEventListener('click', delFunc, true);
			};
			document.querySelector('body').removeEventListener('click', delFunc, true);
			document.querySelector('body').addEventListener('click', delFunc, true);
		});
		
		var no = parseInt( $(post).attr('data-no') );
	}
	
	function expandImage(post) {
		$(post).find('.file-thumb').click(function(e) {
			if( !$(this).has('img').length ) return;
			if( e.button == 1 ) return;
			e.preventDefault();
			
			var url = $(this).attr('href');
			var flg = $(this).has('.-expanded').length ? true : false;
			var tmp = '<div class="-expanded" style="margin: 0.2em; display:block;"><img class="expanded-element expanded-close" style="max-width:100%;  cursor:pointer;" src="' + url + '"></img></div>';
			$(this).hide();
			$(this).after(tmp);
			
			$(this).next('div.-expanded').find('.expanded-close').click(function(e) {
				$(this).parent().parent().children('.file-thumb').each(function() {
					if( !$(this).has('img').length ) return;
					$(this).show();
				});
				
				var cur = $(this).closest('.post').offset().top;
				var h1 = $(window).scrollTop();
				var h2 = h1 + $(window).innerHeight();
				if(cur<h1 || cur>h2) $('html,body').animate({scrollTop:cur}, 0);
				
				$(this).parent().remove();
			});
		});
	}
	
	function _addPopupPost($div, no, highlight=-1) {
		no = parseInt(no);
		if( postNode[no]===undefined ) return 0;
		
		var $new = $(postNode[no]).clone(true).removeClass('reply').removeClass('threadpost');
		$new.find('.file-thumb').show();
		$new.find('div.-expanded').remove();
		$new.find('.file-thumb img').css('height', '').css('width', '');
		if( highlight!=-1 ) $new.find('.quote .qlink[data-no="' + highlight + '"]').css('font-weight', 'bold');
		$div.append($new);
		return 1;
	}
	
	function makeQuoteIndex(post) {
		var no = parseInt( $(post).attr('data-no') );
		var isMobile = $.isMobile();
		$(post).find('.quote .qlink').each(function() {
			var reg = /r(\d+)/g;
			var resto = reg.exec( $(this).attr('href') );
			resto = parseInt( resto[1] );
			
			//回覆下面，回覆自己
			if( resto >= no ) return;
			if( quoteIndex[resto]===undefined ) quoteIndex[resto] = {};
			quoteIndex[resto][no] = 1;
			$(this).attr('data-no', resto);
			
			//Mobile version
			if(isMobile) {
				var $node = $('<a>', {
					'href': $(this).attr('href')
				}).html(' #');
				$(this).after($node);
				$(this).removeAttr('href');
			}
		});
		
		$(post).find('.quote .qlink').mouseenter(function(e) {
			var resto = parseInt( $(this).attr('data-no') );
			var cnt = 0;
			var that = this;
			$popup = $('<div>');
			cnt += _addPopupPost($popup, resto);
			if(cnt) {
				window.setTimeout(function(){popupView.show($popup[0], e.clientX, e.clientY, that);} ,10);
			}
		});
	}
	
	/*
	 * 加 backquote list
	 * 加 backquote handler
	 */
	function makeBackQuote(thread) {
		var isMobile = $.isMobile();
		$(thread).find('.post').each(function() {
			var no = parseInt( $(this).attr('data-no') );
			var cnt = 0, html = '';
			for(var key in quoteIndex[no]) {
				html += '<a class="qlink" href="#r' + key + '" data-no="' + key + '">' +
					'&gt;&gt;' + key +
					'</a>';
				cnt++;
			}
			//刪除舊的 backquote list
			$(this).find('.backquote').remove();
			if(cnt<=0) return;
			
			$(this).append('<div class="backquote"><span class="backquote-count text-button">' + 
				"Replies(" + cnt + "):" +
				'</span></div>'
			);
			$(this).find('.backquote').append(html);
		});
		// mobile ver 只有 Replies(1) 會顯示視窗
		if(!isMobile) {
			$(thread).find('.backquote .qlink').mouseenter(function(e) {
				var no = parseInt( $(this).attr('data-no') );
				var hi = parseInt( $(this).closest('.post').attr('data-no') );
				var $popup = $('<div>');
				var that = this;
				_addPopupPost($popup, no, hi);
				window.setTimeout(function(){popupView.show($popup[0], e.clientX, e.clientY, that);} ,10);
			});
		}
		$(thread).find('.backquote .backquote-count').mouseenter(function(e) {
			var hi = parseInt( $(this).closest('.post').attr('data-no') );
			var $popup = $('<div>');
			var that = this;
			for(var no in quoteIndex[hi]) _addPopupPost($popup, no, hi);
			window.setTimeout(function(){popupView.show($popup[0], e.clientX, e.clientY, that);} ,10);
		});
	}
	
	function makeIDIndex(post) {
		var op = parseInt( $(post).parent().attr('data-no') );
		var no = parseInt( $(post).attr('data-no') );
		var id = $(post).find('.id').first().attr('data-id');
		
		if( !idIndex[op] ) { idIndex[op] = {}; idCount[op] = {}; }
		if( !idIndex[op][id] ) { idIndex[op][id] = {}; idCount[op][id] = 0; }
		idIndex[op][id][no] = 1;
		idCount[op][id]++;
		
		$(post).find('.id').mouseenter(function(e) {
			if( idCount[op][id]<=1 ) return;
			var $popup = $('<div>', {
				'class': 'popup_id',
				'data-id': id
			});
			var that = this;
			
			// popup 略過連續同一個ID
			if( $('.popup_area').find('.popup').length > 0 ) {
				var $ele = $('.popup_area').find('.popup').last();
				if( $ele.hasClass('popup_id') && $ele.attr('data-id')==id ) return;
			}
			for( var no2 in idIndex[op][id] ) _addPopupPost($popup, no2);
			window.setTimeout(function(){popupView.show($popup[0], e.clientX, e.clientY, that);} ,10);
		});
	}
	
	function makeIDCount(thread) {
		var op = parseInt( $(thread).attr('data-no') );
		var idcnt = {};
		
		$(thread).find('.id').each(function() {
			var id = $(this).attr('data-id');
			var cnt = idCount[op][id];
			
			if( !idcnt[id] ) idcnt[id] = 0;
			idcnt[id]++;
			if(cnt!=1) $(this).html("ID:" + id + "(" + idcnt[id] + "/" + cnt + ")");
			
			if(cnt > 1) {
				if(cnt <= 3) $(this).addClass('id3');
				else if(cnt <= 5) $(this).addClass('id5');
				else if(cnt < 9) $(this).addClass('id8');
				else $(this).addClass('id9');
			}
		});
	}
	
	function quickreplyFormInit() {
		//init
		var isMobile = $.isMobile();
		if( $('#quickreply').length==0 ) {
			$mainform = $('#postform_main');
			$node = $('<div id="quickreply"></div>');
			$node.append( $('<form>', {
				id: 'quickreply-form',
				action: $mainform.attr('action'),
				method: $mainform.attr('method'),
				enctype: $mainform.attr('enctype')
			}));
			$('body').append($node);
			
			//生成表單
			$node = $('#quickreply-form');
			$node.append('<div class="quickreply-head">' +
				'<span class="quickreply-title">Quick Reply</span>' +
				'<span class="text-button quickreply-close">[X]</span></div>'
			);
			$('#postform_tbl').find('tr').each(function() {
				if( $(this).find('td').length < 2 ) return;
				if( $(this).find('.g-recaptcha').length > 0 ) return;
				var des = $(this).find('td').eq(0).text().replace(/ /g,'');
				var html = $(this).find('td').eq(1).html();
				var $add = $('<div>' + html + '</div>');
				//不需要標題
				if( $add.find('#fsub').length>0 ) return;
				$add.find('input[type="text"]').attr('placeholder', des);
				$add.find('textarea').attr('placeholder', des);
				$add.find('textarea').removeAttr('cols').css('width', '98%');
				
				$add.find('#fname,#fsub,#fcom,#fupfile').attr('id', '');
				$add.find('#femail').attr('id', 'quickreply-femail');
				$add.find('label[for="femail"]').attr('for', 'quickreply-femail');
				$add.find('#noimg').attr('id', 'quickreply-noimg');
				$add.find('label[for="noimg"]').attr('for', 'quickreply-noimg');
				
				$node.append($add);
			});
			$mainform.find('input:not(#postform_tbl input)').each(function() {
				$node.append( $(this).clone(true) );
			});
			$node.find('input[name="pugid"]').remove();
			$node.append('<input type="hidden" name="pugid" class="pugid">');
			$node.find('.pugid').attr('value', localStorage.getItem('pugid'));
			$node.find('[name="com"]').remove();
			$node.append('<input type="hidden" name="com" value="EID OG SMAPS"></input>');
			if( $('#quickreply').find('input[name="resto"]').length==0 )
				$('#quickreply-form').append('<input type="hidden" name="resto">');
				
			//recaptcha
			if( $('#postform_tbl').find('.g-recaptcha').length > 0 ) {
				useRecaptcha = true;
				var sitekey = $('#postform_tbl').find('.g-recaptcha').first().attr('data-sitekey');
				$('#quickreply-form').append('<div id="quickreply-captcha" data-sitekey="' + sitekey +'"></div>');
				var checkObject = setInterval(function() {
					if(grecaptcha) {
						clearInterval(checkObject);
						recaptchaObject = grecaptcha.render('quickreply-captcha', {
							'sitekey': sitekey
						});
					}
				}, 500);
			}
				
			if(!isMobile) $('#quickreply').draggable({
				containment: 'body',
				handle: '.quickreply-head',
			});
			$('#quickreply .quickreply-close').click(function() {
				$('#quickreply').hide();
			});
			$('#quickreply-form').submit(function(e) {
				e.preventDefault();
				var fd = new FormData(this);
				var url = $(this).attr('action');
				var mime = $(this).attr('enctype');
				var resto = parseInt( $(this).find('input[name="resto"]').first().val() );
				
				$.ajax({
					url: url,
					data: fd,
					type: 'POST',
					mimeType: mime,
					contentType: false,
					cache: false,
					processData: false,
					timeout: ajaxTimeout,
					success: function(data) {
						var bodyHtml = /<body.*?>([\s\S]*)<\/body>/.exec(data);
						if( bodyHtml ) bodyHtml = bodyHtml[1];
						else bodyHtml = '';
						
						var $err = $('<div>' + bodyHtml + '</div>').find('#error');
						//successful
						if( $err.length==0 ) {
							$('#quickreply').hide();
							$('#quickreply textarea').val('');
							var last = parseInt( $(threadNode[resto]).find('.post').last().attr('data-no') );
							_expandThread(resto, last);
						}
						else {
							var msg = $err.find('span').text();
							alert("Error: " + msg);
						}
						if( useRecaptcha ) grecaptcha.reset( recaptchaObject );
					},
					error: function(xhr, text) {
						if(text == 'timeout')
							alert('Error: timeout');
						else
							alert('連線失敗');
						if( useRecaptcha ) grecaptcha.reset( recaptchaObject );
					}
				});
			});
		}
	}
	
	function quickreplyInit(post) {
		var no = $(post).attr('data-no');
		var isMobile = $.isMobile();
		
		$(post).find('.post-head .qlink').click(function(e) {
			//rendering
			var clicke = e;
			var $quickreply = $('#quickreply');
			var op = $(this).closest('.thread').attr('data-no');
			
			if(isMobile) {
				$quickreply.css('top', 0);
				$quickreply.css('left', 0);
				$quickreply.css('max-width', '95%');
				$quickreply.css('max-height', '80%');
			} else {
				var realHeight, realWidth;
				var elm = $quickreply.clone()[0];
				var body = $('body')[0];
				elm.style.top = '0';
				elm.style.left = '0';
				body.appendChild(elm); $(elm).show();
				realHeight = elm.clientHeight;
				realWidth = elm.clientWidth;
				body.removeChild(elm);
				
				var x = Math.max(0, Math.min(clicke.clientX+10, $(window).innerWidth()-realWidth-10));
				var y = Math.max(0, Math.min(clicke.clientY+10, $(window).innerHeight()-realHeight-10));
				$quickreply.css('top' , y);
				$quickreply.css('left', x);
			}
			
			if( $quickreply.is(':hidden') ) {
				$quickreply.find('input[name="resto"]').val(op);
				$quickreply.find('.quickreply-title').html('Quick Reply &gt;&gt;' + op);
				$quickreply.show();
			}
			$('#quickreply textarea').insertAtCaret('>>' + no + "\n");
		});
	}
	
	function _expandThread(op, after=-1) {
		var pos = 0;
		var INF = 2000000000;
		var nos = [];
		$(threadNode[op]).find('.post').each(function(){
			nos.push( parseInt($(this).attr('data-no')) );
		});
		nos.push(INF);
		
		$.getJSON('pixmicat.php?mode=module&load=mod_ajax&action=thread&html=true&op=' + op, function(json){
			json = json['posts'];
			var len = json.length;
			
			//threadNode[op] = this;
			
			for(var i=0; i<len; i++) if(json[i]['no']>after) {
				while(nos[pos] < json[i]['no']) pos++;
				if(nos[pos] > json[i]['no']) {
					if(nos[pos] != INF) $(postNode[nos[pos]]).before(json[i]['html']);
					else $(threadNode[op]).find('.post').last().after(json[i]['html']);
					postNode[json[i]['no']] = $('.post[data-no="' + json[i]['no'] + '"]')[0];
					onPostLoaded(json[i]['no']);
				}
			}
			
			onThreadUpdated(op);
			//onThreadLoaded(op);
			//移除按鈕
			if(after==-1) $(threadNode[op]).find('.-expand-thread').each(function(){
				if( $(this).parent().next().prop('tagName')=="BR" ) $(this).parent().next().remove();
				$(this).parent().remove();
			});
		});
	}
	
	function expandButtonInit(post) {
		var no = $(post).attr('data-no');
		$(post).find('.warn_txt2').each(function() {
			newhtml = $(this).html().replace(/要閱讀所有回應請按下回應連結。/, '<span class="-expand-thread text-button">[展開]</span>');
			$(this).html(newhtml);
		});
		$(post).find('.-expand-thread').click(function(){
			_expandThread(no);
		});
	}
	
	/*
	 * onPostLoaded: 加按鈕、加功能...
	 * onThreadUpdated: 如更新ID數量, 更新...
	 * onThreadLoaded: 如封鎖文章等
	 */
	function onPostLoaded(no) {
		for(var i=0; i<onPostLoadedFn.length; i++)
			onPostLoadedFn[i]( postNode[no] );
	}
	
	function onThreadLoaded(op) {
		
	}
	
	function onThreadUpdated(op) {
		for(var i=0; i<onThreadUpdatedFn.length; i++)
			onThreadUpdatedFn[i]( threadNode[op] );
	}
	
	onPostLoadedFn.push(initPost);
	onPostLoadedFn.push(hideTooLong);
	onPostLoadedFn.push(expandImage);
	onPostLoadedFn.push(makeQuoteIndex);
	onPostLoadedFn.push(makeIDIndex);
	onPostLoadedFn.push(quickreplyInit);
	onPostLoadedFn.push(expandButtonInit);
	
	onThreadUpdatedFn.push(makeBackQuote);
	onThreadUpdatedFn.push(makeIDCount);
	
	//MAIN
	$(document).ready(function(){
		var st = new Date();
		
		quickreplyFormInit();
		
		$('body').append('<div class="popup_area"></div>');
		popupView = new UI.PopupView($('body')[0])
		
		$('.thread').each(function() {
			var op = parseInt( $(this).attr('data-no') );
			threadNode[op] = this;
			$(this).find('.post').each(function() {
				var no = parseInt( $(this).attr('data-no') );
				postNode[no] = this;
				onPostLoaded(no);
			});
			onThreadUpdated(op);
			onThreadLoaded(op);
		});
		
		console.log("Time diff: " + (new Date()-st) + "ms");
		
		//TEST
		/* st = new Date();
		for(var i=0; i<1000000; i++) {
			var no = $(postNode[0]).attr('data-no');
		}
		console.log("100w * $(element): " + (new Date()-st) + "ms"); */
		
		/*
		st = new Date();
		for(var i=0; i<1000000; i++) {
			var no = postNode[0].getAttribute("data-no");
		}
		console.log("100w * (getAttribute): " + (new Date()-st) + "ms");*/
	});
})();
