   
<template>
    <section>
        <div>
            <b-modal ref="frmConceptoGasto" id="frmConceptoGasto" ok-title="Cerrar" ok-variant="danger" ok-only size="lg"
                centered title="Gestionar Gasto por Importación" @ok="listaConceptoGasto">
                <!-- Diseño del Formulario -->
            </b-modal>
        </div>
        <div>
            <b-overlay id="overlay-background" :variant="variant" :opacity="opacity" :blur="blur">
                <b-card border-variant="info">
                    <!-- <b-card-title style="text-align: center">Listado de Cajas</b-card-title> -->
                    <!-- <b-card-body> -->
                    <b-row>
                        <b-col sm="4" md="5" xl="6" lg="6" class="mb-1">
                            <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="outline-success"
                                :class="$store.state.app.classButton" @click="validaOperacion($store.state.app.TipoAccion)">
                                <feather-icon :icon="$store.state.app.botonIcono" class="mr-50" />
                                <span>Registrar Gasto Para:</span>
                                <span>{{ $store.state.app.idUtilitario }}</span>
                            </b-button>
                        </b-col>
                        <b-col sm="8" md="7" xl="6" lg="6">
                            <b-form-group label-for="filter-input">
                                <b-input-group>
                                    <b-form-input id="filter-input" v-model="filter" debounce="200" type="search"
                                        placeholder="Esccribe Para Buscar"></b-form-input>
                                    <b-input-group-append>
                                        <b-button :disabled="!filter" variant="danger"
                                            @click="filter = ''">Limpiar</b-button>
                                    </b-input-group-append>
                                </b-input-group>
                            </b-form-group>
                        </b-col>
                    </b-row>
                    <b-row>
                        <b-col>
                            <b-table :items="conceptosGasto" :fields="TipoGastofields" hover="true" responsive="sm"
                                :busy="isBusy" outlined :sticky-header="stickyHeader" :bordered="true" stacked="sm" small
                                :style="{ fontSize: fontSize }">
                                <template v-slot:cell(TipoGasto)="data">
                                    <b>{{ data.value }}</b>
                                </template>

                                <template v-slot:cell(conceptos)="data">

                                    <b-table :items="data.item.conceptos" :fields="conceptoFields" :filter="filter"
                                        @filtered="onFiltered" hover="true" responsive="sm" :busy="isBusy" outlined
                                        :sticky-header="stickyHeader" :bordered="true" stacked="sm" small
                                        :style="{ fontSize: fontSize }">
                                        <template v-slot:cell(cgasNombre)="conceptoData">
                                            <b-row>
                                                <b-col>
                                                    <b>{{ conceptoData.value }}</b>
                                                </b-col>
                                            </b-row>

                                        </template>
                                        <template #cell(precioBs)="row">
                                            <b-form-input v-model="row.value" type="number" min="1"
                                                @input="actualizarCantidadBs(row.item, row.value)" ref="precioBs"
                                                :state="row.value > 0? true:false"
                                                :trigger="'hover focus'" class="v-b-tooltip-dark text-center" />
                                        </template>

                                        <template #cell(precioUSD)="row">
                                            <b-form-input v-model="row.value" type="number" min="1" ref="precioUSD"
                                                :show="row.value === 0" :trigger="'hover focus'"
                                                class="v-b-tooltip-dark text-center" />
                                        </template>

                                        <template #table-busy>
                                            <div class="text-center text-danger my-2">
                                                <b-spinner class="align-middle"></b-spinner>
                                                <strong>Cargando...</strong>
                                            </div>
                                        </template>
                                    </b-table>
                                
                                </template>
                            </b-table>                               
                        </b-col>
                    </b-row>
                    <!-- </b-card-body> -->
                </b-card>
            </b-overlay>
        </div>

    </section>
</template>
<script>
import {
    VBTooltip, BFormFile, BFormDatepicker, BRow, BModal, VBModal, BAvatar, BCardTitle,
    BCardBody, BCardHeader, BCard, BDropdown, BDropdownItem, BButton, BFormSelect, BCol, BFormGroup, BFormInput,
    BFormCheckbox, BForm, BFormText, BFormDatalist, BBadge, BTable, BMedia, BFormTextarea, BInputGroupAppend, BInputGroup, BOverlay,
    BSpinner, BFormValidFeedback, BFormInvalidFeedback,
} from "bootstrap-vue";
import Ripple from "vue-ripple-directive";
import vSelect from 'vue-select'

export default {
    components: {
        VBTooltip, vSelect, BFormFile, BFormValidFeedback, BFormInvalidFeedback, BOverlay, BFormDatepicker,
        BInputGroupAppend, BInputGroup, BRow, BModal, VBModal, BTable, BAvatar, BCardTitle, BCardBody, BCardHeader,
        BCard, BDropdown, BDropdownItem, BButton, BFormSelect, BFormTextarea, BCol, BFormGroup, BFormInput, BFormCheckbox,
        BForm, BMedia, BFormText, BFormDatalist, BBadge, BSpinner,
    },
    directives: {
        "b-tooltip": VBTooltip,
        Ripple,
    },
    data() {
        return {
            fontSize: "",
            TipoGastofields:
                [
                    { key: "TipoGasto", label: "TIPO GASTO", sortable: true },
                    { key: "conceptos", label: "CONCEPTOS", sortable: true }
                ],
            conceptosGasto: [

            ],
            conceptoFields:
                [
                    { key: "cgasNombre", label: "Nombre", sortable: true, tdClass: "col-nombre w-25" },
                    { key: "precioBs", label: "Monto Bs", sortable: false, tdClass: "text-center text-bold w-15" },
                    { key: "precioUSD", label: "Monto $", sortable: false, tdClass: "text-center text-bold w-15" },

                ],
            TipoAccion: null,
            stickyHeader: true,
            transProps: {
                // Transition name
                name: "flip-list",
            },
            mesReporte: "",
            shows: true,
            isBusy: false,
            totalRows: 1,
            loaded: false,
            filter: "",
            filterOn: [],
            idGenera: 0,
            id: 0,
            fecha: 0,
            fechaRegistro: "",
            Loading: "",
            estado: "",
            items: [],
            fields: [
                { key: "id", label: "CODIGO", sortable: true },
                { key: "cgasNombre", label: "CONCEPTO GASTO", sortable: true },
                { key: "Acción", sortable: false },
            ],

            show: false,
            variant: "dark",
            opacity: 0.85,
            blur: "2px",
            // selected: [],
        };
    },
    computed: {
        sortOptions() {
            // Create an options list from our fields
            return this.fields
                .filter((f) => f.sortable)
                .map((f) => {
                    return { text: f.label, value: f.key };
                });
        },
    },
    created() {
        this.sizeTablas()
    },
    mounted() {
        this.listaConceptoGasto();
    },
    methods: {
        actualizarCantidadBs(item, nuevaCantidad) {
            item.precioBs = nuevaCantidad;

            // Actualizar el concepto en conceptosGasto
            const tipoGastoIndex = this.conceptosGasto.findIndex(tipoGasto => tipoGasto.TipoGasto === item.TipoGasto);
            const conceptoIndex = this.conceptosGasto[tipoGastoIndex].conceptos.findIndex(concepto => concepto.cgasId === item.cgasId);

            // Verificar si el concepto existe en conceptosGasto
            if (tipoGastoIndex !== -1 && conceptoIndex !== -1) {
                // Actualizar el monto en el array conceptosGasto
                this.$set(this.conceptosGasto[tipoGastoIndex].conceptos, conceptoIndex, {
                    ...this.conceptosGasto[tipoGastoIndex].conceptos[conceptoIndex],
                    precioBs: nuevaCantidad,
                });
            }
        },

        async Guardar() {

            const formData = new FormData();
            formData.append("impNumero", this.$store.state.app.idUtilitario)
            formData.append("gastosImportacion",JSON.stringify(this.conceptosGasto))
            try {
                const response = await this.$http.post("guardarGastosImportacion", formData);

                if (response.status === 201) {
                    this.UsuarioAlerta("success", response.data.mensaje);
                    // this.listaConceptoGasto();
                } else {
                    this.UsuarioAlerta("error", response.data.error);
                }
            } catch (error) {
                console.error(error.message);
                this.UsuarioAlerta("error", error.response);
            }
        },
        validaOperacion(accion) {
            if (accion === "guardarGI") { this.Guardar() }
            if (accion === "editar") { this.mofificar() }
            if (accion === "ver") { alert("ajecutara el ver") }

        },
        UsuarioAlerta(variant, msj) {
            let title, confirmButtonClass, showClass;

            if (variant === "success") {
                title = "Buen Trabajo";
                confirmButtonClass = "btn btn-success";
                showClass = "animate__animated animate__bounceIn";
            } else if (variant === "error") {
                title = "¡Error!";
                confirmButtonClass = "btn btn-danger";
                showClass = "btn btn-danger animate__animated animate__rubberBand";
            } else if (variant === "warning") {
                title = "Precaución";
                confirmButtonClass = "btn btn-warning";
                showClass = "animate__animated animate__wobble";
            } else {
                // Puedes agregar más casos según tus necesidades.
            }

            this.$swal({
                title: title,
                text: msj,
                icon: variant,
                customClass: {
                    confirmButton: confirmButtonClass,
                },
                showClass: {
                    confirmButton: showClass,
                },
                buttonsStyling: true,
            });
        },
        ControlaEliminar(item, index) {
            this.boxTwo = "";
            this.$bvModal
                .msgBoxConfirm(
                    "El Articulo  " + " : " + item["cgasId"] + " Serán Eliminados",
                    {
                        title: "Advertencia",
                        size: "sm",
                        okVariant: "success",
                        okTitle: "Continuar",
                        cancelTitle: "Cancelar",
                        cancelVariant: "danger",
                        hideHeaderClose: true,
                        centered: true,
                    }
                )
                .then((value) => {
                    this.boxTwo = value;
                    if (value === true) {
                        this.eliminarProducto(item, index);
                    }
                });
        },
        eliminarProducto(index) {
            if (index >= 0 && index < this.conceptosGasto.length) {
                this.conceptosGasto.splice(index, 1);
            }
        },
        eliminar(item) {
            let me = this;
            const axios = require("axios").default;
            const params = new URLSearchParams();
            params.append('Id', item["cgasId"]);
            var url = "api/auth/eliminarConceptoGasto";
            me.isBusy = true;
            axios
                .post(url, params)
                .then(function (response) {

                    if (response.status == 201) {
                        me.UsuarioAlerta("success", response.data.mensaje);
                        me.listaConceptoGasto()
                        me.isBusy = false;
                    } else {
                        me.UsuarioAlerta("error", response.data.error);
                    }

                })
                .catch((e) => {
                    me.isBusy = false;
                    me.UsuarioAlerta("error", e.response.data.error);
                    console.log("danger", "No se Realizó la Operación: " + e);
                });
        },
        onFiltered(filteredItems) {
            // Trigger pagination to update the number of buttons/pages due to filtering
            this.totalRows = filteredItems.length;
        },
        limpiarVariables() {
            let me = this;
            me.listaConceptoGasto();
            me.shows = false;
            me.loaded = false;
            me.Loading = "";
            me.filter = null;
            me.filterOn = [];
            me.gestion = 0;
            me.mes = 0;
            me.iExiste = 0;
            me.estado = "";
        },
        async listaConceptoGasto() {
            try {
                const response = await this.$http.get("listaConceptoGasto");
                const resp = response.data;

                const lista = Object.keys(resp).map(tipoGasto => (
                    {
                        TipoGasto: resp[tipoGasto].TipoGasto,
                        conceptos: resp[tipoGasto].conceptos.map
                            (concepto =>
                            (
                                {
                                    cgasId: concepto.cgasId,
                                    cgasNombre: concepto.cgasNombre,
                                    tgasId: concepto.tgasId,
                                    cgasActivo: concepto.cgasActivo,
                                    cgasFechaCreacion: concepto.cgasFechaCreacion,
                                    precioBs: 0,
                                }
                            )
                            ),
                    }));

                this.conceptosGasto = lista;
            } catch (error) {
                console.error(error.message);
                this.UsuarioAlerta("error", error.response);
                this.showOverlay = false;
            }
        },
        onContext(ctx) {
            // The date formatted in the locale, or the `label-no - date - selected` string
            this.formatted = ctx.selectedFormatted;
            // The following will be an empty string until a valid date is entered
            this.mesReporte = ctx.selectedYMD;
        },
        sizeTablas() {
            const anchoVentana = window.innerWidth;

            if (anchoVentana <= 576) {
                // Dispositivo móvil pequeño
                this.fontSize = 'xx-small';
            } else if (anchoVentana <= 768) {
                // Dispositivo móvil o tableta
                this.fontSize = 'small';
            } else if (anchoVentana <= 992) {
                // Tableta o dispositivo de tamaño medio
                this.fontSize = 'medium';
            } else {
                // Pantalla de escritorio
                this.fontSize = 'mediun';
            }
        },
    },
};
function MesActual(fecha, formato) {
    // 2022-09-02 21:51:48.000000"
    const map = {
        dd: fecha.getDate(),
        mm: fecha.getMonth() + 1,
        yy: fecha.getFullYear().toString(),
        yyyy: fecha.getFullYear(),
        HH: fecha.getHours(),
        MM: fecha.getMinutes(),
        SS: fecha.getSeconds(),
    };
    // HH:MM:SS
    return formato.replace(/yy|mm|dd|HH|MM|SS/gi, (matched) => map[matched]);
}
</script>
<style lang="scss" >
@import '~@resources/scss/vue/libs/vue-select.scss';

.custom-badge {
    width: 30px;
    height: 15px;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
<style>
.col-nombre {
    width: 80%;
    /* Ajusta el porcentaje según tus necesidades */
}

.col-accion {
    width: 80%;
    /* Ajusta el porcentaje según tus necesidades */
}
</style>
      