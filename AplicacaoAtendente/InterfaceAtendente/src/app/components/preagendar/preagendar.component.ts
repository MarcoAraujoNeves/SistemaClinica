import { Component, ChangeDetectorRef, ViewChild, ViewEncapsulation } from "@angular/core";
import { FuncaoService } from "../../services/funcao/funcao.service";
import { EmpresasService } from "../../services/empresas/empresas.service";
import { SubgrupoService } from "../../services/subgrupo/subgrupo.service";
import { FuncaoexameService } from "../../services/funcaoexame/funcaoexame.service";
import { PacienteService } from "../../services/paciente/paciente.service";
import { ExameService } from "../../services/exames/exames.service";
import { TipoconsultaService } from "../../services/tipoconsulta/tipoconsulta.service";
import { MatSnackBar } from '@angular/material/snack-bar';
import { FormGroup, FormBuilder, Validators, FormControl, } from "@angular/forms";
import { map, startWith } from "rxjs/operators";
import { Observable } from "rxjs";
import { paciente } from "../../services/paciente/paciente";
import { empresas } from "../../services/empresas/empresas";
import { funcao } from "../../services/funcao/funcao";
import { subgrupo } from "../../services/subgrupo/subgrupo";
import { tipoconsulta } from "../../services/tipoconsulta/tipoconsulta";
import { ConsultaService } from 'src/app/services/consulta/consulta.service';
import { HttpErrorResponse } from '@angular/common/http';
import { ConsultaExameProfissionalService } from 'src/app/services/consulta_exame_profissional/consulta-exame-profissional.service';
import { MatStepper } from '@angular/material';
import { EstadosService } from 'src/app/services/estado/estado.service';
import { PreagendarService } from 'src/app/services/preagendar/preagendar.service';
import { NovoPacienteService } from '../../services/novo_paciente/novo-paciente.service'
import { NovaEmpresaService } from '../../services/nova_empresa/nova-empresa.service'
import { NovaFuncaoService } from '../../services/nova_funcao/nova-funcao.service'
import { NovoSubgrupoService } from '../../services/novo_subgrupo/novo-subgrupo.service';
import { SocketService } from '../../services/socket/socket.service';


import { RELOAD_AGENDADOS, RELOAD_PACIENTES, DONT_RELOAD_PACIENTES, RELOAD_EMPRESAS, DONT_RELOAD_EMPRESAS, BROADCAST, RELOAD_FUNCOES, DONT_RELOAD_FUNCOES, RELOAD_SUBGRUPOS, DONT_RELOAD_SUBGRUPOS } from '../constants';
import { setData } from '../date';

@Component({
    selector: "app-preagendar",
    templateUrl: "preagendar.component.html",
    styleUrls: ["preagendar.component.css"]
})
export class PreAgendamento {
    constructor(
        private empresaService: EmpresasService,
        private funcaoService: FuncaoService,
        private subGrupoService: SubgrupoService,
        private funcaoExameService: FuncaoexameService,
        private pacientesService: PacienteService,
        private exameService: ExameService,
        private tipoConsultaService: TipoconsultaService,
        private formBuilder: FormBuilder,
        private cdr: ChangeDetectorRef,
        private matSnackBar: MatSnackBar,
        private consultaService: ConsultaService,
        private cepService: ConsultaExameProfissionalService,
        private estadoService: EstadosService,
        private preagendarService: PreagendarService,
        private novoPacienteService: NovoPacienteService,
        private novaEmpresaService: NovaEmpresaService,
        private socketService: SocketService,
        private novaFuncaoService: NovaFuncaoService,
        private novoSubgrupoService: NovoSubgrupoService
    ) { }

    "use strict";
    encapsulation: ViewEncapsulation.None;
    @ViewChild("stepper", { static: false }) stepper: MatStepper;
    empresas: empresas[] = [];
    funcoes: funcao[] = [];
    subGrupos: subgrupo[] = [];
    funcaoexames: any = [];
    pacientes: paciente[] = [];
    TipoConsulta: tipoconsulta[] = [];
    exames: any = [];
    selectedFunction: string = null;
    consultaName;
    firstForm: FormGroup;
    secondForm: FormGroup;
    thirdForm: FormGroup;
    funcao;
    exameObj;
    dataExame;
    subgrupoValue;
    dateExameDisplay;

    //Parte do autocomplete
    filteredPacientes: Observable<paciente[]>;
    filteredEmpresas: Observable<empresas[]>;
    filteredFuncao: Observable<funcao[]>;
    filteredSubGrupo: Observable<subgrupo[]>;

    ngOnInit() {
        this.configurarFormulario();
        this.checkState();
        this.subgrupoValue = false;
        this.carregarFuncoes();
        this.carregarEmpresas();
        this.carregarPacientes();
        this.carregarExames();
        this.carregarTipoConsulta();

        this.filtrarSubGrupo();
        this.filtrarPacientes();
        this.filtrarEmpresas();
        this.filtrarFuncao();
    }

    ngAfterContentChecked(): void {
        this.cdr.detectChanges();
    }
    checkPacientes() {
        this.novoPacienteService.currentNovoPaciente.subscribe(message => {
            if (message == RELOAD_PACIENTES) {
                this.carregarPacientes();
                this.novoPacienteService.updateTabelaPaciente(DONT_RELOAD_PACIENTES);
            }
        })
    }
    checkEmpresas() {
        this.novaEmpresaService.currentEmpresas.subscribe(message => {
            if (message == RELOAD_EMPRESAS) {
                this.carregarEmpresas();
                this.novaEmpresaService.updateTabelaEmpresas(DONT_RELOAD_EMPRESAS)
            }
        })
    }
    checkFuncoes() {
        this.novaFuncaoService.currentFuncoes.subscribe(message => {
            if (message == RELOAD_FUNCOES) {
                this.carregarFuncoes();
                this.novaFuncaoService.updateTabelaFuncoes(DONT_RELOAD_FUNCOES)
            }
        })
    }

    checkSubgrupos() {
        this.novoSubgrupoService.currentSubgrupos.subscribe(message => {
            if (message == RELOAD_SUBGRUPOS) {
                this.carregarSubGrupos();
                this.novoSubgrupoService.updateTabelaSubgrupos(DONT_RELOAD_SUBGRUPOS)
            }
        })
    }

    checkState() {
        this.checkPacientes();
        this.checkEmpresas();
        this.checkFuncoes();
        this.checkSubgrupos();
    }
    //carga de informações
    async carregarExames() {
        await this.exameService.listaDeExames().subscribe(exames => {
            for (let exame of exames) {
                exame['checked'] = false;
                this.exames.push(exame);
            }
        });
    }
    async carregarEmpresas() {
        this.empresas = [];
        await this.empresaService.listaDeEmpresas().subscribe(empresas => {
            for (let empresa of empresas) {
                this.empresas.push(empresa);
            }
        });
    }
    async carregarFuncoes() {
        await this.funcaoService.listaDeFuncoes().subscribe(funcoes => {
            for (let funcao of funcoes) {
                this.funcoes.push(funcao);
            }
        });
    }
    async carregarSubGrupos() {
        let codFuncao = this.firstForm.value.funcao.codFuncao;
        this.subGrupos = new Array();
        this.filtrarSubGrupo()
        await this.subGrupoService.listaDeSubgrupo().subscribe(subgrupos => {
            for (let subgrupo of subgrupos) {
                if (subgrupo.codFuncao == codFuncao) {
                    this.subGrupos.push(subgrupo);
                }
            }
        });

    }
    async carregarPacientes() {
        this.pacientes = []
        await this.pacientesService.listaDePacientes().subscribe(pacientes => {
            for (let paciente of pacientes) {
                this.calculateAge(paciente);
                this.pacientes.push(paciente);
            }
        });
    }
    calculateAge(paciente) {
        const ageDifMs = Date.now() - new Date(paciente.nascimento).getTime();
        const ageDate = new Date(ageDifMs); // miliseconds from epoch
        paciente['age'] = Math.abs(ageDate.getUTCFullYear() - 1970);
    }

    async carregarTipoConsulta() {
        await this.tipoConsultaService
            .listaDeTipoConsultas()
            .subscribe(tiposconsultas => {
                for (let tp of tiposconsultas) {
                    tp["checked"] = false;
                    this.TipoConsulta.push(tp);
                }
            });
    }
    //A seguir vem a lógica dnome => nome ? this._filtroPacientes(nome) : this.pacientes.slice()os autocompletes
    filtrarPacientes() {
        this.filteredPacientes = this.firstForm.controls['paciente'].valueChanges.pipe(
            startWith(""),
            map(value => (typeof value === "string" ? value : value.nome)),
            map(nome =>
                nome ? this._filtroPacientes(nome) : this.pacientes.slice()
            )
        );
    }

    private _filtroPacientes(nome: string): paciente[] {
        const filterValue = nome.toLocaleLowerCase();
        return this.pacientes.filter(
            paciente => paciente.nome.toLowerCase().indexOf(filterValue) === 0
        );
    }
    displayAutocompletePaciente(paciente?: paciente): string | undefined {
        return paciente ? paciente.nome : undefined;
    }

    filtrarEmpresas() {
        this.filteredEmpresas = this.firstForm.controls['empresa'].valueChanges.pipe(
            startWith(""),
            map(value => (typeof value === "string" ? value : value.nome)),
            map(nome =>
                nome ? this._filtroEmpresa(nome) : this.empresas.slice()
            )
        );
    }

    private _filtroEmpresa(nome: string): empresas[] {
        const filterValue = nome.toLocaleLowerCase();
        return this.empresas.filter(
            empresa => empresa.nome.toLowerCase().indexOf(filterValue) === 0
        );
    }

    displayAutocompleteEmpresa(empresa?: empresas): string | undefined {
        return empresa ? empresa.nome : undefined;
    }

    filtrarFuncao() {
        this.filteredFuncao = this.firstForm.controls['funcao'].valueChanges.pipe(
            startWith(""),
            map(value => (typeof value === "string" ? value : value.nome)),
            map(nome =>
                nome ? this._filtroFuncao(nome) : this.funcoes.slice()
            )
        );
    }

    private _filtroFuncao(nome: string): funcao[] {
        const filterValue = nome.toLocaleLowerCase();
        return this.funcoes.filter(
            funcao => funcao.nome.toLowerCase().indexOf(filterValue) === 0
        );
    }
    displayAutocompleteFuncao(funcao?: funcao): string | undefined {
        return funcao ? funcao.nome : undefined;
    }
    filtrarSubGrupo() {
        this.filteredSubGrupo = this.firstForm.controls['subgrupo'].valueChanges.pipe(
            startWith(""),
            map(value => (typeof value === "string" ? value : value.nome)),
            map(nome =>
                nome ? this._filtroSubGrupo(nome) : this.subGrupos.slice()
            )
        );
    }
    private _filtroSubGrupo(nome: string): subgrupo[] {
        const filterValue = nome.toLowerCase();
        return this.subGrupos.filter(
            subgrupo => subgrupo.nome.toLowerCase().indexOf(filterValue) === 0
        );
    }
    displayAutocompleteSubGrupo(subgrupo?: subgrupo): string | undefined {
        return subgrupo ? subgrupo.nome : undefined;
    }
    setExamesFalse() {
        this.exames.forEach(exame => {
            exame['checked'] = false;
        });
    }
    //Dados do formulário
    async examesObrigatorios() {
        this.setExamesFalse();
        let selectedFunction = this.firstForm.value.funcao.codFuncao;
        await this.funcaoExameService.lerFuncaoEmpresa(selectedFunction).subscribe(exames => {
            for (let exame of exames) {
                if (exame['codExame'] != null) {
                    this.changeCheckbox(exame['codExame']);
                }
            }
        })

    }
    changeCheckbox(codExame) {
        for (let exame of this.exames) {
            if (exame['codExame'] === codExame) {
                exame['checked'] = true;
            }
        }
    }

    configurarFormulario() {
        this.firstForm = this.formBuilder.group({
            paciente: ['', Validators.required],
            empresa: ['', Validators.required],
            subgrupo: [null],
            funcao: ['', Validators.required],
        });
        this.secondForm = this.formBuilder.group({
            checkboxExames: [true, Validators.required],
            consulta: ['', Validators.required],
            dataExame: ['', Validators.required]
        });
        this.thirdForm = this.formBuilder.group({

        });
    }

    setFormatData() {
        this.dataExame = this.secondForm.value.dataExame;
        this.secondForm.controls.dataExame.setValue(
            this.secondForm.value.dataExame.slice(0, 19).replace("T", " ")
        );
        this.tipoConsultaService.lerTipoConsulta(this.secondForm.value.consulta).subscribe(response => {
            this.consultaName = response.nome
        })
        this.exameObj = this.selectedExamesObj();
        if (this.secondForm.value.dataExame == undefined) {
            return
        }
        let data = this.secondForm.value.dataExame;
        this.dateExameDisplay = setData(data);
    }
    selectedExamesObj() {
        return this.exames
            .filter(exame => exame.checked == true)
            .map(exame => exame);
    }
    selectedExames() {
        return this.exames
            .filter(exame => exame.checked == true)
            .map(exame => exame.codExame);
    }
    get selectedTipoConsultas() {
        return this.TipoConsulta.filter(
            tipoconsulta => tipoconsulta["checked"] == true
        ).map(tipoconsulta => tipoconsulta["codTipoConsulta"]);
    }
    subgrupoInput() {

        this.subgrupoValue = !this.subgrupoValue;
        if (this.subgrupoValue) {
            this.firstForm.addControl('subgrupo', new FormControl(null, Validators.required))
        } else {
            this.firstForm.removeControl('subgrupo');
            this.firstForm.updateValueAndValidity();
        }
    }

    reloadFormValues(form) {
        Object.keys(form.controls).forEach(key => {
            form.get(key).setValue('', Validators.required);
        })
    }

    reloadFormErrors(form) {
        Object.keys(form.controls).forEach(key => {
            form.get(key).setErrors(null);
        });
    }

    reloadStepper() {
        this.reloadFormValues(this.firstForm);
        this.reloadFormValues(this.secondForm);

        this.reloadFormErrors(this.firstForm);
        this.reloadFormErrors(this.secondForm);

        this.stepper.selectedIndex = 0;
        this.stepper.linear = true;

        this.stepper._steps.forEach(step => {
            step.completed = false,
                step.stepControl.markAsPending()
        })
        //this.secondForm.controls['checkboxExames'].setValue(true);
        //this.setExamesFalse();
    }

    alocarProfissionalExame(consulta) {
        this.cepService.alocarProfissionalExame(consulta.codConsulta, this.secondForm.value.checkboxExames).subscribe(response => {
            this.preagendarService.updateTabelaAgendados(RELOAD_AGENDADOS);
        }, (err: HttpErrorResponse) => {
            // this.openSnackBar("Não foi possível alocar um profissional para o exame!",0)
        })
    }

    agendarConsulta(consulta) {
        this.estadoService.agendarEmConsulta(consulta).subscribe(response => {
            this.openSnackBar("Consulta agendada com sucesso!", 1);
            this.socketService.emit('broadcast', BROADCAST);
        }, (err: HttpErrorResponse) => {
            this.openSnackBar("Não foi possível cadastrar a consulta, algum dado deve estar incorreto!", 0);
        });
    }

    async createConsulta() {


        this.secondForm.controls['checkboxExames'].setValue(
            this.selectedExames()
        )
        if (this.firstForm.invalid) {
            this.openSnackBar("Algum dado inicial está incorreto", 0);
            return;
        }
        if (this.secondForm.invalid) {
            this.openSnackBar("Algum dado da consulta está incorreto", 0);
            return;
        }

        await this.consultaService.cadastrarConsulta(this.firstForm.value, this.secondForm.value).subscribe(async response => {
            await this.alocarProfissionalExame(response);
            await this.agendarConsulta(response['codConsulta']);
            this.socketService.emit('broadcast', BROADCAST);
            // this.openSnackBar("Consulta cadastrada com sucesso!", 1)
        }, (err: HttpErrorResponse) => {
            this.openSnackBar("Não foi possível cadastrar a consulta!", 0)
        })
    }
    openSnackBar(mensagem, nivel) {
        switch (nivel) {
            case 1:
                nivel = "alerta-sucesso";
                break;
            case 0:
                nivel = 'alerta-fracasso';
                break;
        }
        this.matSnackBar.open(mensagem, "", { duration: 2000, panelClass: nivel });
        this.reloadStepper();
    }
}