import { Component, OnInit } from '@angular/core';
import { SidenavComponent } from '../../sidenav/sidenav.component';

import { MatSnackBar } from '@angular/material/snack-bar';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';

import { FuncaoService } from '../../../services/funcao/funcao.service';
import { HttpErrorResponse } from '@angular/common/http';
@Component({
    selector: 'app-nova-funcao',
    templateUrl: './nova-funcao.component.html'
})
export class NovaFuncaoComponent implements OnInit {

    formularioNovaFuncao: FormGroup;
    executandoRequisicao: Boolean = false;

    constructor(
        private formBuilder: FormBuilder,
        public sideNav: SidenavComponent,
        private funcaoService: FuncaoService,
        private _snackBar: MatSnackBar
    ) { }

    ngOnInit() {
        this.sideNav.activeView = "Funções > Nova Função";
        this.configurarFormulario();
    }

    configurarFormulario() {
        this.formularioNovaFuncao = this.formBuilder.group({
            nome: [null, Validators.required],
            descricao: [null, Validators.required],
            setor: [null, Validators.required]
        });
    }

    createFuncao() {

        let form = this.formularioNovaFuncao.value;
        //Testar se algum campo está vazio
        for (let campo in form) {
            if (form[campo] == null) return;
        }
        //Exibe a barra de progresso
        this.executandoRequisicao = true;

        //Armazenando a resposta para dar feedback ao usuário
        this.funcaoService.cadastrarFuncao(form).subscribe(response => {
            
                this.openSnackBar("Cadastro efetuado!", 1);
                // Reinicia os estados do formulário, também eliminando os erros de required
                this.formularioNovaFuncao.reset();
                Object.keys(this.formularioNovaFuncao.controls).forEach(key => {
                    this.formularioNovaFuncao.get(key).setErrors(null);
                });
            
        },(err: HttpErrorResponse) => {
            this.openSnackBar("Cadastro não foi efetuado!", 1);
        });

        this.executandoRequisicao = false;
    }

    openSnackBar(mensagem, nivel) {
        switch (nivel) {
            case 1:
                nivel = 'alerta-sucesso';
                break;
            case 0:
                nivel = 'alerta-fracasso';
                break;
        }
        this._snackBar.open(mensagem, "", { duration: 2000, panelClass: nivel });
    }
}
