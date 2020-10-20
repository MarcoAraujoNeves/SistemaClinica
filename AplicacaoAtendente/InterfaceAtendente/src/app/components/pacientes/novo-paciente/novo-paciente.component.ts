import { Component, OnInit } from "@angular/core";
import { SidenavComponent } from "../../sidenav/sidenav.component";

import { MatSnackBar } from "@angular/material/snack-bar";
import { FormGroup, FormBuilder, Validators } from "@angular/forms";

import { PacienteService } from "../../../services/paciente/paciente.service";
import { PacientesComponent } from "../pacientes.component";

import { NovoPacienteService } from '../../../services/novo_paciente/novo-paciente.service';
import { RELOAD_PACIENTES } from '../../constants';

@Component({
    selector: "app-novo-paciente",
    templateUrl: "./novo-paciente.component.html",
    styleUrls: ["./novo-paciente.component.css"]
})
export class NovoPacienteComponent implements OnInit {

    selected='none';

    formularioNovoPaciente: FormGroup;
    executandoRequisicao: Boolean = false;

    constructor(
        private att: PacientesComponent,
        private formBuilder: FormBuilder,
        public sideNav: SidenavComponent,
        private pacienteService: PacienteService,
        private _snackBar: MatSnackBar,
        private novoPacienteService:NovoPacienteService
    ) {}

    ngOnInit() {
        this.configurarFormulario();
    }

    SoLetras_Validator = '^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]+$';
    CPF_Validator = '(([0-9]{3}.[0-9]{3}.[0-9]{3}-[0-9]{2})|([0-9]{11}))';
    RG_Validator = '(([0-9]{2}.[0-9]{3}.[0-9]{3}-[0-9]{1}))'

    configurarFormulario() {
        this.formularioNovoPaciente = this.formBuilder.group({
            nome: [null, Validators.required],
            cpf: [null],
            rg: [null],
            sexo: [null],
            nascimento: [null],
        });
    }

    createPaciente() {

        let form = this.formularioNovoPaciente.value;


         //Testa se algum campo não esta esta seguindo o padrão de validação
        if (this.formularioNovoPaciente.invalid) {
            this.executandoRequisicao = false;
            this._snackBar.open("Dados em vermelho incorretos ou em branco, não foi possivel cadastrar !!!", null, {
                duration: 6000,
            });
            return;
        }

        //Exibe a barra de progresso
        this.executandoRequisicao = true;

        //Armazenando a resposta para dar feedback ao usuário
        this.pacienteService.cadastrarPaciente(form).subscribe(
            response => {
                this.openSnackBar("Cadastro efetuado com sucesso !!!", 1);
                // Reinicia os estados do formulário, também eliminando os erros de required
                this.att.ngOnInit();
                this.novoPacienteService.updateTabelaPaciente(RELOAD_PACIENTES);
                this.formularioNovoPaciente.reset();
                Object.keys(this.formularioNovoPaciente.controls).forEach(
                    key => {
                        this.formularioNovoPaciente.get(key).setErrors(null);
                    }
                );
            },
            error => {
                this.openSnackBar("Erro! Cadastro não realizado.", 0);
            }
        );

        this.executandoRequisicao = false;
    }

    openSnackBar(mensagem, nivel) {
        switch (nivel) {
            case 1:
                nivel = "alerta-sucesso";
                break;
            case 0:
                nivel = "alerta-fracasso";
                break;
        }
        this._snackBar.open(mensagem, "", {
            duration: 2000,
            panelClass: nivel
        });
    }
}
