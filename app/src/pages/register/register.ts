import {Component} from "@angular/core";
import {NavController, AlertController} from "ionic-angular";
import {LoginPage} from "../login/login";
import { SimplejsProvider } from "../../providers/simplejs/simplejs";
import { LoadingController } from 'ionic-angular';

@Component({
  selector: 'page-register',
  templateUrl: 'register.html'
})
export class RegisterPage {
  
  objeto = {
    nome:null,
    email:'',
    senha: null
  }
  public msgVazios:String;
  private erro:Boolean;
  constructor(public nav: NavController,public alertCtrl:AlertController,public simplejs :SimplejsProvider,public loadingCtrl: LoadingController) {
    
  }

  // sign up
  signUp(obj) {
    const loader = this.loadingCtrl.create({
      content: "Please wait...",
    });
    loader.present();
    this.msgVazios =  "Por favor preencha os campos :<br>";
    this.erro = false;
    for (const Item of Object.keys(obj)) {
      if(obj[Item] == null){
        this.erro = true;
        this.msgVazios += `-${Item}<br>`;
      }
    }

    //se houver campos que não foram preenchidos
    if(this.erro){
      loader.dismiss()
      const alert = this.alertCtrl.create({
        title: 'Atenção!',
        subTitle: `${this.msgVazios}`,
        buttons: ['OK']
      })
      alert.present();
    }else{
      loader.dismiss()
      this.simplejs.postApi("teste",obj)
      .then(res => {
        console.log(res);
        
      });
    }
  
    
    // this.nav.setRoot(MainTabsPage);
  }

  // go to login page
  login(obj) {
    this.nav.setRoot(LoginPage);
  }
}
