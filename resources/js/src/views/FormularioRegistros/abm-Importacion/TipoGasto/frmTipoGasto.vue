<template>
    <section>
        <b-card border-variant="info">
            <b-row>
                <b-col md="6">
                    <b-form-group>
                        <label class="d-inline d-lg-flex">Nombre del Tipo de Gasto</label>
                        <b-form-input v-model="tgasNombre" :state="tgasNombre.length ? true : false" required />
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

            isBusy: false,
            filter: "",
            stickyHeader: true,
            tgasNombre: "",
        }
    },
    directives: {
        Ripple,
    },
    mounted() {
        if (this.$store.state.app.TipoAccion === "editar" || this.$store.state.app.TipoAccion === "ver") {

            this.TraerTipoGasto()
        }
    },
    methods: {
        //acciones 
        TraerTipoGasto() {
            let me = this;
            const axios = require("axios").default;
            const params = new URLSearchParams();
            params.append('Id', this.$store.state.app.idUtilitario);
            me.items = [];

            var url = "api/auth/TraerTipoGasto";
            me.loaded = false;
            var lista = [];
            axios
                .post(url, params)
                .then(function (response) {
                    var resp = response.data;
                    for (let i = 0; i < resp.length; i++) {
                        me.tgasNombre = resp[i].tgasNombre

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
                // const hoy = new Date();

                // const axios = require("axios").default;
                const formData = new FormData();

                me.items = [];
                var urlm = "api/auth/agregarTipoGasto";
                me.loaded = false;
                me.isBusy = true;
                formData.append("tgasNombre", me.tgasNombre);
                // formData.append("UsuarioId", this.$store.state.app.UsuarioId);

                const response = await this.$http.post("agregarTipoGasto", formData)
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
            var urlm = "api/auth/modificarTipoGasto";
            me.loaded = false;
            me.isBusy = true;
            formData.append('Id', this.$store.state.app.idUtilitario);
            formData.append("tgasNombre", me.tgasNombre);
            axios
                .post(urlm, formData)
                .then(function (response) {
                    if (response.status == 200) {
                        me.showOverlay = false;
                        me.UsuarioAlerta("success",response.data.mensaje);
                        me.isBusy = false;

                    } else {
                        me.UsuarioAlerta("error",response.data.error);
                    }
                })
                .catch((e) => {
                    me.UsuarioAlerta("error",e.response.data.error);
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