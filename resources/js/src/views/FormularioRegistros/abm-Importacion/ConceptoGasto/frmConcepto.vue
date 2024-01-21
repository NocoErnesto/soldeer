<template>
    <section>
        <b-overlay :show="showOverlay" rounded="sm" no-fade>
            <b-card border-variant="info">
                <b-row>
                    <b-col md="6">
                        <b-form-group>
                            <label class="d-inline d-lg-flex">Tipo Gasto</label>
                            <v-select v-model="selectedTipoGasto" :options="booksTipoGasto" label="title"
                                placeholder="Seleccionar" style="color:brown ;">
                                <template #option="{ title, icon }">
                                    <feather-icon :icon="icon" size="16" class="align-middle mr-50" />
                                    <span> {{ title }}</span>
                                </template>
                            </v-select>
                        </b-form-group>
                    </b-col>
                    <b-col md="6">
                        <b-form-group>
                            <label class="d-inline d-lg-flex">Nombre del Concepto Gasto</label>
                            <b-form-input v-model="cgasNombre" :state="cgasNombre.length ? true : false"  />
                        </b-form-group>
                    </b-col>

                    <!-- Agrega otros campos del formulario aquí -->
                </b-row>
                <b-row>
                    <b-col>
                        <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" :variant="$store.state.app.variant"
                            :class="$store.state.app.classButton" @click="validaOperacion($store.state.app.TipoAccion)">
                            <feather-icon :icon="$store.state.app.botonIcono" class="mr-50" />
                            <span class="align-middle">{{ $store.state.app.botonTexto }} </span>
                        </b-button>
                    </b-col>
                </b-row>

            </b-card>


        </b-overlay>

    </section>
</template>

<script>
import {
    BFormSpinbutton,
    BImg,
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
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
    components: {
        ToastificationContent,
        BImg,
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
        BFormSpinbutton
    },
    data() {
        return {
            showOverlay: false,
            isBusy: false,
            filter: "",
            stickyHeader: true,
            cgasNombre: "",
            selectedTipoGasto: {
                id: "",
                title: "",
                icon: 'ListIcon',
            },
            booksTipoGasto: [
                {
                    id: "",
                    title: "",
                    icon: 'ListIcon',
                },
            ],
        }
    },
    directives: {
        Ripple,
    },
    mounted() {

        if (this.$store.state.app.TipoAccion === "editar" || this.$store.state.app.TipoAccion === "ver") {

            this.TraeConceptoGasto()
        }
        this.cbxListaConcepto()
    },
    methods: {
        //acciones 

        async cbxListaConcepto() {
            try {
                let me = this;
                const lista = [];
                me.booksTipoGasto = []
                const response = await this.$http.get("listaTipogasto")
                const resp = response.data;
                for (let i = 0; i < resp.length; i++) {

                    lista.push({
                        id: resp[i].tgasId,
                        title: resp[i].tgasNombre,
                        icon: 'ListIcon',
                    });
                }
                me.booksTipoGasto = lista;
            } catch (error) {
                console.log(error.message);
                this.UsuarioAlerta("error", error.response);
                this.showOverlay = false;

            }

        },
        TraeConceptoGasto() {
            let me = this;
            const axios = require("axios").default;
            const params = new URLSearchParams();
            params.append('Id', this.$store.state.app.idUtilitario);
            me.items = [];

            var url = "api/auth/TraeConceptoGasto";
            me.loaded = false;
            var lista = [];
            axios
                .post(url, params)
                .then(function (response) {
                    var resp = response.data;
                    for (let i = 0; i < resp.length; i++) {
                        me.selectedTipoGasto = {
                            id: resp[i].tgasId,
                            title: resp[i].tgasNombre,
                        }
                        me.cgasNombre = resp[i].cgasNombre

                    }
                    me.items = lista;
                    me.loaded = true;
                })
                .catch((e) => {
                    alert("error al obtener los datos Lista Articulo " + e);
                });
        },
        async Guardar() {

            try {
                let me = this;
                me.showOverlay = true;
                const hoy = new Date();

                const axios = require("axios").default;
                const formData = new FormData();

                me.items = [];
                me.loaded = false;
                me.isBusy = true;
                formData.append("tgasId", me.selectedTipoGasto.id);
                formData.append("cgasNombre", me.cgasNombre);
                const response = await this.$http.post("agregarConceptoGasto", formData)
                if (response.status === 201) {
                    this.showOverlay = false;
                    me.UsuarioAlerta("success", response.data.mensaje);
                    me.isBusy = false;
                }
            } catch (error) {
                this.showOverlay = false;
                this.UsuarioAlerta("error", error.response.data.error);
            }
        },
        modificar() {
            let me = this;
            debugger
            me.showOverlay = true;
            const hoy = new Date();

            const axios = require("axios").default;
            const formData = new FormData();

            me.items = [];
            var urlm = "api/auth/modificarConceptoGasto";
            me.loaded = false;
            me.isBusy = true;
            formData.append('Id', this.$store.state.app.idUtilitario);
            formData.append("cgasNombre", me.cgasNombre);
            formData.append("tgasId", me.selectedTipoGasto.id);
            axios
                .post(urlm, formData)
                .then(function (response) {
                    if (response.status == 200) {
                        me.showOverlay = false;
                        me.UsuarioAlerta("success", response.data.mensaje);
                        me.isBusy = false;

                    } else {
                        me.UsuarioAlerta("error", response.data.error);
                    }
                })
                .catch((e) => {
                    me.UsuarioAlerta("error", e.response.data.error);
                    me.showOverlay = false;
                    console.log("danger", "No se Realizó la Operación: " + e);
                });
        },

        //eventos 
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

        validaOperacion(accion) {
            if (accion === "guardar") { this.Guardar() }
            if (accion === "editar") { this.modificar() }
            if (accion === "ver") { alert("ajecutara el ver") }

            0
        },

        ControlaEliminar(item, index) {
            this.boxTwo = "";
            this.$bvModal
                .msgBoxConfirm(
                    "El Registro  " + " : " + item["title"] + " Serán Eliminados",
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
                        this.eliminarProducto(index);
                    }
                });
        },
        clickAccion(item, index, accion) {
            if (accion === "eliminar") {
                this.ControlaEliminar(item, index)
            }
        },

    },
}

</script>

<style></style>