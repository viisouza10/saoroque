import { Injectable } from '@angular/core';
import { Http, URLSearchParams } from '@angular/http';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/toPromise';
import 'rxjs/add/operator/timeout';

import { FileTransfer, FileUploadOptions, FileTransferObject } from '@ionic-native/file-transfer';
import { Network } from '@ionic-native/network';

import { ToastController } from 'ionic-angular';

@Injectable()
export class SimplejsProvider {

  /*
    Para funcionar corretamento o SIMPLETS é necessario 
    instalar no projeto todas as bibliotecas que estão
    no @ionic-native e coloca-las no app.module
  */

  public user;
  public avatar;

  toast: any;


  urlAPI: string = 'http://saoroque.mac/api'; //Local
 

  constructor(
    public http: Http,
    public network: Network,
    public toastCtrl: ToastController,
    private transfer: FileTransfer
  ) {

    this.network.onDisconnect().subscribe(() => {
      this.toast = this.toastCtrl.create({
        message: 'Foi identificado que sua conexão com a Internet foi encerrada.',
        position: 'top'
      });
      this.toast.present();
    });

    this.network.onConnect().subscribe(() => {
      this.toast.dismiss()
    });

  }

  postApi(path:String, obj) {
    let filtro = new URLSearchParams();
    
    filtro.append('objeto', JSON.stringify(obj));
    console.log(filtro);
    
    return this.http
      .post(`${this.urlAPI}/${path}/`, filtro)
      .timeout(10000)
      .map((res) => res)
      .toPromise()
      .then((sucess) => { 
        return JSON.parse((<any>sucess)._body);
      })
      .catch((err) => {
        if (err.name == "TimeoutError" || (err.status == 0 && err.type == 3)) {
          err.mensagem = "Ocorreu uma falha de comunicação, tente novamente mais tarde ou verifique sua conexão com a internet"
        }
        return err
      })
  }

  getApi(path) {
    return this.http
      .get(`${this.urlAPI}/${path}/`)
      .timeout(10000)
      .map((res) => res)
      .toPromise()
      .then((sucess) => {
        return JSON.parse((<any>sucess)._body)
      })
      .catch((err) => {
        if (err.name == "TimeoutError") {
          err.mensagem = "Ocorreu uma falha de comunicação, tente novamente mais tarde ou verifique sua conexão com a internet"
        }
        return err
      })
  }

  getCEP(numCep: number) {
    return this.http
      .get(`http://cep.alphacode.com.br/action/cep/${numCep}`)
      .map((res) => res)
      .toPromise()
      .then((sucess) => {
        return JSON.parse((<any>sucess)._body)
      })
      .catch((err) => {
        console.error(err)
        return err
      })
  }

  uploadFile(fileURL) {

    let options: FileUploadOptions = {
      fileKey: 'file',
      fileName: fileURL.substr(fileURL.lastIndexOf('/') + 1),
      params: {
        id: this.user.id
      }
    }

    const fileTransfer: FileTransferObject = this.transfer.create();

    fileTransfer
      .upload(fileURL, encodeURI(this.urlAPI + "/UploadFile"), options)
      .then((data) => {
        console.log(data);
        let imagem = JSON.parse(data.response)
        this.avatar = imagem.data;
        localStorage.setItem('lazerecultura.avatar', this.avatar);
      })
      .catch((error) => {
        this.avatar = false;
        console.error(error)
      });

  }

  validarCPF(cpf: string) {
    return new Promise((resolve, reject) => {
      cpf = cpf.replace(/[^\d]+/g, '');
      if (cpf.length != 11 ||
        cpf == "00000000000" ||
        cpf == "11111111111" ||
        cpf == "22222222222" ||
        cpf == "33333333333" ||
        cpf == "44444444444" ||
        cpf == "55555555555" ||
        cpf == "66666666666" ||
        cpf == "77777777777" ||
        cpf == "88888888888" ||
        cpf == "99999999999") {
        reject('error');
        return false;
      }

      let add = 0;
      for (let i = 0; i < 9; i++) {
        add += parseInt(cpf.charAt(i)) * (10 - i);
      }
      let rev = 11 - (add % 11);
      if (rev == 10 || rev == 11) {
        rev = 0;
      }
      if (rev != parseInt(cpf.charAt(9))) {
        reject('error');
        return false;
      }

      add = 0;
      for (let i = 0; i < 10; i++) {
        add += parseInt(cpf.charAt(i)) * (11 - i);
      }
      rev = 11 - (add % 11);
      if (rev == 10 || rev == 11)
        rev = 0;
      if (rev != parseInt(cpf.charAt(10))) {
        reject('error');
        return false;
      }

      resolve('sucesso');
      return true;

    })
  }

}
