   
<template>
    <section>
      <div>
        <b-modal ref="frmTipoGasto" id="frmTipoGasto" ok-title="Cerrar" ok-variant="danger" ok-only size="lg" centered
          title="Registro de Marcas" @ok="listaTipogasto">
          <!-- Diseño del Formulario -->
        <frm-tipo-gasto></frm-tipo-gasto>
        </b-modal>
      </div>
      <div>
        <b-overlay id="overlay-background" :variant="variant" :opacity="opacity" :blur="blur">
          <b-card border-variant="info">
            <!-- <b-card-title style="text-align: center">Listado de Cajas</b-card-title> -->
            <!-- <b-card-body> -->
            <b-row>
              <b-col sm="4" md="5" xl="6" lg="6" class="mb-1">
                <!-- Boton Modal -->
                <b-button v-ripple.400="'rgba(113, 102, 240, 0.15)'" v-b-modal.frmTipoGasto variant="success"
                  @click="clickAccion('', 'guardar')" :class="{ 'd-none': $store.state.app.isCrea }">
                  Nuevo Registro
                </b-button>
              </b-col>
              <b-col sm="8" md="7" xl="6" lg="6">
                <b-form-group label-for="filter-input">
                  <b-input-group>
                    <b-form-input id="filter-input" v-model="filter" debounce="200" type="search"
                      placeholder="Esccribe Para Buscar"></b-form-input>
                    <b-input-group-append>
                      <b-button :disabled="!filter" variant="danger" @click="filter = ''">Limpiar</b-button>
                    </b-input-group-append>
                  </b-input-group>
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col>
                <!-- Tabla --> <!-- Listado -->
                <b-table id="tabla-lista-marcas" :items="items" :fields="fields" :filter="filter" @filtered="onFiltered"
                  hover responsive="sm" :busy="isBusy" outlined :sticky-header="stickyHeader">
  
                  <template #cell(Acción)="row">
                    <b-row>
                      <b-col>
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="flat-warning" v-b-tooltip.hover.v-dark
                          title="Seguir Editando" class="btn-icon" :class="{ 'd-none': $store.state.app.isEdita }"
                          @click="clickAccion(row.item, ('editar'))">
                          <feather-icon icon="EditIcon" />
                        </b-button>
                      </b-col>
                      <b-col>
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="flat-success" v-b-tooltip.hover.v-dark
                          title="Ver Detalle" class="btn-icon" :class="{ 'd-none': $store.state.app.isVer }"
                          @click="clickAccion(row.item, ('ver'))">
                          <feather-icon icon="EyeIcon" />
                        </b-button>
                      </b-col>
                      <b-col>
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" variant="flat-danger"
                          class="btn-icon rounded-circle" :class="{ 'd-none': $store.state.app.isElimina }"
                          @click="clickAccion(row.item, ('eliminar'))">
                          <feather-icon icon="TrashIcon" />
                        </b-button>
                      </b-col>
                    </b-row>
                  </template>
                  <template #table-busy>
                    <div class="text-center text-danger my-2">
                      <b-spinner class="align-middle"></b-spinner>
                      <strong>Cargando...</strong>
                    </div>
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
    VBTooltip,
    BFormFile,
    BFormDatepicker,
    BRow,
    BModal,
    VBModal,
    BAvatar,
    BCardTitle,
    BCardBody,
    BCardHeader,
    BCard,
    BDropdown,
    BDropdownItem,
    BButton,
    BFormSelect,
    BCol,
    BFormGroup,
    BFormInput,
    BFormCheckbox,
    BForm,
    BFormText,
    BFormDatalist,
    BBadge,
    BTable,
    BMedia,
    BFormTextarea,
    BInputGroupAppend,
    BInputGroup,
    BOverlay,
    BSpinner,
    BFormValidFeedback,
    BFormInvalidFeedback,
  } from "bootstrap-vue";
  import Ripple from "vue-ripple-directive";
  import vSelect from 'vue-select'

import FrmTipoGasto from './frmTipoGasto.vue';

  export default {
    components: {
      VBTooltip,
      vSelect,
      BFormFile,
      BFormValidFeedback,
      BFormInvalidFeedback,
      BOverlay,
      BFormDatepicker,
      BInputGroupAppend,
      BInputGroup,
      BRow,
      BModal,
      VBModal,
      BTable,
      BAvatar,
      BCardTitle,
      BCardBody,
      BCardHeader,
      BCard,
      BDropdown,
      BDropdownItem,
      BButton,
      BFormSelect,
      BFormTextarea,
      BCol,
      BFormGroup,
      BFormInput,
      BFormCheckbox,
      BForm,
      BMedia,
      BFormText,
      BFormDatalist,
      BBadge,
      BSpinner,
      FrmTipoGasto,
    },
    directives: {
      "b-tooltip": VBTooltip,
      Ripple,
    },
    data() {
      return {
  
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
          { key: "tgasNombre", label: "TIPO GASTO", sortable: true },
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
    mounted() {
      this.listaTipogasto();
    },
    methods: {
      clickAccion(item, accion) {
  
  
        if (accion === "guardar") {
          this.$store.dispatch('app/cambiarTipoAccion', { tipo: accion, variant: 'success', icono: 'SaveIcon', texto: 'Guardar', Bclass: '' })
        }
        if (accion === "editar") {
  
          this.$store.dispatch('app/cambiaId', item["id"])
          this.$store.dispatch('app/cambiarTipoAccion', { tipo: accion, variant: 'primary', icono: 'SaveIcon', texto: 'Modificar', Bclass: '' })
  
          this.$refs["frmTipoGasto"].show();
        }
        if (accion === "ver") {
          this.$store.dispatch('app/cambiaId', item["id"])
          this.$store.dispatch('app/cambiarTipoAccion', { tipo: accion, variant: 'success', icono: 'SaveIcon', texto: 'Guardar', Bclass: 'd-none' })
  
          this.$refs["frmTipoGasto"].show();
        }
        if (accion === "eliminar") {
          this.ControlaEliminar(item)
        }
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
      ControlaEliminar(item) {
        this.boxTwo = "";
        this.$bvModal
          .msgBoxConfirm(
            "El Articulo  " + " : " + item["id"] + " Serán Eliminados",
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
              this.eliminar(item);
            }
          });
      },
      eliminar(item) {
        let me = this;
        const axios = require("axios").default;
        const params = new URLSearchParams();
        params.append('id', item["id"]);
        var url = "api/auth/EliminarTipoGasto";
        me.isBusy = true;
        axios
          .post(url, params)
          .then(function (response) {
  
            if (response.status == 201) {
              me.UsuarioAlerta("success",response.data.mensaje);
              me.listaTipogasto()
              me.isBusy = false;
            } else {
              me.UsuarioAlerta("error",response.data.error);
            }
  
          })
          .catch((e) => {
            me.UsuarioAlerta("error",e.response.data.error);
            console.log("danger", "No se Realizó la Operación: " + e);
          });
      },
      onFiltered(filteredItems) {
        // Trigger pagination to update the number of buttons/pages due to filtering
        this.totalRows = filteredItems.length;
      },
      limpiarVariables() {
        let me = this;
        me.listaTipogasto();
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
  
  
      listaTipogasto() {
        let me = this;
        const axios = require("axios").default;
        const params = new URLSearchParams();
        // params.append('email', me.email);
        me.items = [];
        me.isBusy = true;
        var url = "api/auth/listaTipogasto";
        me.loaded = false;
        var lista = [];
        axios
          .get(url)
          .then(function (response) {
            var resp = response.data;
            for (let i = 0; i < resp.length; i++) {
              lista.push({
                id: resp[i].tgasId,
                tgasNombre: resp[i].tgasNombre,
  
              });
            }
            me.items = lista;
            me.isBusy = false;
            me.loaded = true;
          })
          .catch((e) => {
            alert("error al obtener los datos del Listado Tipo Gasto " + e);
          });
      },
      onContext(ctx) {
        // The date formatted in the locale, or the `label-no - date - selected` string
        this.formatted = ctx.selectedFormatted;
        // The following will be an empty string until a valid date is entered
        this.mesReporte = ctx.selectedYMD;
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
      