// ----------------------------------------------------------------------------
// markItUp!
// ----------------------------------------------------------------------------
// Copyright (C) 2011 Jay Salvat
// http://markitup.jaysalvat.com/
// ----------------------------------------------------------------------------
// Html tags
// http://en.wikipedia.org/wiki/html
// ----------------------------------------------------------------------------
// Basic set. Feel free to add more tags
// ----------------------------------------------------------------------------
var mySettings = {
	previewParserPath:	'~/../../templates/default/preview.php',
	nameSpace:       "html", // Useful to prevent multi-instances CSS conflict
    onShiftEnter:    {keepDefault:false, replaceWith:'<br />\n'},
    onCtrlEnter:     {keepDefault:false, openWith:'\n<p>', closeWith:'</p>\n'},
    onTab:           {keepDefault:false, openWith:'     '},
    markupSet:  [
   		{name:'Titres', className:'titres', dropMenu: [
		        {name:'Heading 1', key:'1', openWith:'<h1(!( class="[![Classe]!]")!)>', closeWith:'</h1>', placeHolder:'Votre titre ici...' },
		        {name:'Heading 2', key:'2', openWith:'<h2(!( class="[![Classe]!]")!)>', closeWith:'</h2>', placeHolder:'Votre titre ici...' },
		        {name:'Heading 3', key:'3', openWith:'<h3(!( class="[![Classe]!]")!)>', closeWith:'</h3>', placeHolder:'Votre titre ici...' },
		        {name:'Heading 4', key:'4', openWith:'<h4(!( class="[![Classe]!]")!)>', closeWith:'</h4>', placeHolder:'Votre titre ici...' },
		        {name:'Heading 5', key:'5', openWith:'<h5(!( class="[![Classe]!]")!)>', closeWith:'</h5>', placeHolder:'Votre titre ici...' },
		        {name:'Heading 6', key:'6', openWith:'<h6(!( class="[![Classe]!]")!)>', closeWith:'</h6>', placeHolder:'Votre titre ici...' }
			]
		},
        {name:'Paragraphe', key:'P', openWith:'<p(!( class="[![Classe]!]")!)>', closeWith:'</p>'  },
        {separator:'---------------' },
        {name:'Gras', key:'B', openWith:'<strong>', closeWith:'</strong>' },
        {name:'Italique', key:'I', openWith:'<em>', closeWith:'</em>'  },
        {name:'Barré', key:'D', openWith:'<del>', closeWith:'</del>' },
        {separator:'---------------' },
        {name:'Ul', openWith:'<ul>\n', closeWith:'</ul>\n' },
        {name:'Ol', openWith:'<ol>\n', closeWith:'</ol>\n' },
        {name:'Li', openWith:'<li>', closeWith:'</li>' },
        {separator:'---------------' },
        {name:'Explorateur',key:'E', className:'browse',
		  openWith:'',
		  closeWith:'',
		   beforeInsert:function() { 
			   	var idTextarea = this.id.replace('id="markItUpId_','');
				idTextarea = idTextarea.replace('"','');
                $('<iframe src="medias.php?id='+idTextarea+'&editor=html"></iframe>').modal();
            }
		},
        {name:'Lien externe', key:'T', openWith:'<a href="[![Lien:!:http://]!]"(!( title="[![Titre]!]")!) onclick="window.open(this.href);return false;">', closeWith:'</a>', placeHolder:'Le texte du lien...' },
        {name:'Lien', key:'L', openWith:'<a href="[![Lien:!:http://]!]"(!( title="[![Titre]!]")!)>', closeWith:'</a>', placeHolder:'Le texte du lien...' },
        {name:'Générateur de tableaux HTML', key:'J', 
			className:'tableHtmlgenerator', 
			placeholder:"Votre texte ici...",
			replaceWith:function(markItUp) {
				var rowsInHead = prompt("Combien de lignes d'entête?"),
					cols = prompt("Combien de colonnes?"),
					rows = prompt("Combien de lignes de corps?"),
					html = "<table>\n";
				if (cols != null && rows != null){
					if (markItUp.altKey) {
						html += " <tr>\n";
						for (var c = 0; c < cols; c++) {
							html += "! [![TH"+(c+1)+" text:]!]\n";	
						}
						html+= " </tr>\n";
					}
					if (rowsInHead != 0 && rowsInHead != null) {
						html+= "<thead>\n";
					
						for (var ro = 0; ro < rowsInHead; ro++) {
							html+= "\t<tr>\n";
							for (var c = 0; c < cols; c++) {
								html += "\t\t<th>"+(markItUp.placeholder||"")+"</th>\n";	
							}
							html+= "\t</tr>\n";
						}
						html += "</thead>\n";
					}
					html+= "<tbody>\n";
						for (var r = 0; r < rows; r++) {
							html+= "\t<tr>\n";
							for (var c = 0; c < cols; c++) {
								html += "\t\t<td>"+(markItUp.placeholder||"")+"</td>\n";	
							}
							html+= "\t</tr>\n";
						}
					html += "</tbody>\n</table>\n";
				} else {
					html = '';
				}
				return html;
			}
		},
        {separator:'---------------' },
        {name:'Nettoyer', key:'K', replaceWith:function(h) { return h.selection.replace(/<(.*?)>/g, "") } },
        {name:'Agrandir', key:'G', call:'', className:"large", afterInsert:function(h) {
		    $(h.textarea).animate( { height:'1000px' } );
		  }
		},
		{name:'Diminuer', key:'S', call:'', className:"small", afterInsert:function(h) {
		    $(h.textarea).animate( { height:'320px' } );
		  }
		},
        {name:'Prévisualisation', key:'=', call:'preview', className:'preview' },
        {name:'Editeur HTML', call:'', className:'Html' }
    ]
}
