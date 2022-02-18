// Botao Salvar Plugin for HTMLArea-3.0
// Implementation by Mauro Charao
// Distributed under the same terms as HTMLArea itself.
// Deve ser criada a funcao BotaoSalvarClick() na pagina chamadora

function Btsalvar(editor) {
  this.editor = editor;
  var self = this;
  // register the toolbar buttons provided by this plugin
  editor.config.registerButton("SA-salvar", Btsalvar.I18N["SA-salvar"], editor.imgURL("salvar.gif", "Btsalvar"),
                               false, function(editor, id) { self.buttonPress(editor, id); } );
  editor.config.toolbar[1].push("SA-salvar");
};

Btsalvar._pluginInfo = {
  name         : "Btsalvar",
  version      : "1.0",
  developer    : "Mauro Charão",
  developer_url: "http://nead.rs.senai.br",
  c_owner      : "SENAI-RS",
  sponsor      : "",
  sponsor_url  : "",
  license      : "htmlArea"
};

Btsalvar.prototype.buttonPress = function(editor, id) {
  switch(id) {
    case "SA-salvar":
      Btsalvar.editor = editor;
      Btsalvar.init = true;
      BotaoSalvarClick();
    break;
  }
};
