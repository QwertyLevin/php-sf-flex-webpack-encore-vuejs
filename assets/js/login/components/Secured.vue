<template>
    <div class="books">
        <label class="title">
            <h5 class="title">Secured page</h5>
        </label>
        <q-toolbar color="primary">
            <q-toolbar-title>
                Hello {{ user.username }}
            </q-toolbar-title>
        </q-toolbar>
    </div>
</template>

<script>
import { QToolbar, QToolbarTitle, Notify } from 'quasar-framework/dist/quasar.mat.esm'
import { IsLoggedInObservable } from '../../lib/login'
export default {
    name: 'Secured',
    components: {
        QToolbar,
        QToolbarTitle,
        Notify
    },
    data() {
        return {
            user: {},
        }
    },
    created() {
        const defaultUser = {
            user: {
                username: 'inconnu'
            }
        }

        this.user = defaultUser

        if (typeof localStorage == 'undefined') {
            Notify.create({
                message: `No localStorage feature available in the browser`,
                type: 'warning'
            })
        }

        IsLoggedInObservable.subscribe(isLoggedIn => {
            if (!isLoggedIn
                || !isLoggedIn.me) {
                this.user = defaultUser
            } else {
                this.user = isLoggedIn.me
            }
        })
    },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
