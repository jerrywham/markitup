// -------------------------------------------------------------------
// markItUp!
// -------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// -------------------------------------------------------------------
// MarkDown tags example
// http://en.wikipedia.org/wiki/Markdown
// http://daringfireball.net/projects/markdown/
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
mySettings = {
	previewParserPath:	'~/../../templates/markdown/preview.php',
	nameSpace:       "markItUp",
	onShiftEnter:		{keepDefault:false, openWith:'\n\n'},
	onTab:           {keepDefault:false, openWith:'     '},
	markupSet: [
		//{name:'Titre de niveau 1', key:'1', placeHolder:'Votre titre ici...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '=') } },
		//{name:'Titre de niveau 2', key:'2', placeHolder:'Votre titre ici...', closeWith:function(markItUp) { return miu.markdownTitle(markItUp, '-') } },
		{name:'Titres', className:'titres', dropMenu: [
				{name:'Titre de niveau 1', key:'1', openWith:'# ', placeHolder:'Votre titre ici...' },
				{name:'Titre de niveau 2', key:'2', openWith:'## ', placeHolder:'Votre titre ici...' },
				{name:'Titre de niveau 3', key:'3', openWith:'### ', placeHolder:'Votre titre ici...' },
				{name:'Titre de niveau 4', key:'4', openWith:'#### ', placeHolder:'Votre titre ici...' },
				{name:'Titre de niveau 5', key:'5', openWith:'##### ', placeHolder:'Votre titre ici...' },
				{name:'Titre de niveau 6', key:'6', openWith:'###### ', placeHolder:'Votre titre ici...' }
			]
		},
		{separator:'---------------' },		
		{name:'Gras', key:'B', openWith:'**', closeWith:'**'},
		{name:'Italique', key:'I', openWith:'_', closeWith:'_'},
		{separator:'---------------' },
		{name:'Liste non ordonnée', openWith:'- ' },
		{name:'Liste ordonnée', openWith:function(markItUp) {
			return markItUp.line+'. ';
		}},
		{separator:'---------------' },
		{name:'Explorateur', key:'E', className:'browse',
		  openWith:'',
		  closeWith:'',
		   beforeInsert:function() { 
			   	var idTextarea = this.id.replace('id="markItUpId_','');
				idTextarea = idTextarea.replace('"','');
                $('<iframe src="medias.php?id='+idTextarea+'&editor=markdown"></iframe>').modal();
            }
		},
		{name:'Email', key:'M', replaceWith:'<[![Email]!]>'},
		{name:'Lien externe', key:'T', openWith:'[', closeWith:']([![Url:!:http://]!] "[![Titre]!]")(ext)', placeHolder:'Le texte du lien ici...' },
		{name:'Lien', key:'L', openWith:'[', closeWith:']([![Url:!:http://]!] "[![Titre]!]")', placeHolder:'Le texte du lien ici...' },
		{separator:'---------------' },
		{name:'Générateur de tableaux MarkDown', key:'T',
			className:'tableMarkdowngenerator', 
			placeholder:" Votre texte ici...   ",
			replaceWith:function(markItUp) {
				var cols = prompt("Combien de colonnes?"),
					rows = prompt("Combien de lignes?"),
					mkdoTab = "\n";
				if (cols != null && rows != null){
					//Entête
					for (var c = 0; c < cols; c++) {
						mkdoTab += (markItUp.placeholder||"");
						if (c != cols-1 || cols == 1) {
						mkdoTab += "|";
						}	
					}
					mkdoTab += "\n";
					for (var c = 0; c < cols; c++) {
						mkdoTab += ":-------------------:";
						if (c != cols-1 || cols == 1) {
							mkdoTab += "|";
						}
					}
					mkdoTab += "\n";
					//Corps
					for (var r = 0; r < rows; r++) {
						for (var c = 0; c < cols; c++) {
							mkdoTab += (markItUp.placeholder||"");
							if (c != cols-1 || cols == 1) {
							mkdoTab += "|";
							}	
						}
						mkdoTab += "\n";
					}
				} else {
					mkdoTab = '';
				}
				return mkdoTab;
			}
		},
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
		{separator:'---------------'},	
		{name:'Citation', openWith:'> '},
		{name:'Bloc de Code / Code', openWith:'(!(\t|!|`)!)', closeWith:'(!(`)!)'},
		{separator:'---------------'},
		{name:'Agrandir', key:'G', call:'', className:"large", afterInsert:function(h) {
		    $(h.textarea).animate( { height:'1000px' } );
		  }
		},
		{name:'Diminuer', key:'S', call:'', className:"small", afterInsert:function(h) {
		    $(h.textarea).animate( { height:'320px' } );
		  }
		},
		{name:'Prévisualisation', key:'=', call:'preview', className:"preview"},
		{name:'Editeur Markdown : Cliquez pour obtenir de l\'aide', key:'H', call:'', className:"Markdown",
		   beforeInsert:function() {
                $('<iframe src="~/../../../plugins/markitup/lang/fr-help.php?modal=1"></iframe>').modal();
            }
        }
	]
}

// mIu nameSpace to avoid conflict.
miu = {
	markdownTitle: function(markItUp, char) {
		heading = '';
		n = $.trim(markItUp.selection||markItUp.placeHolder).length;
		for(i = 0; i < n; i++) {
			heading += char;
		}
		return '\n'+heading;
	}
}