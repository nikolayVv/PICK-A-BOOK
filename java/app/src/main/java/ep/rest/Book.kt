package ep.rest

import java.io.Serializable

data class Book (
        val knjiga_id: Int = 0,
        val knjiga_avtor: String = "",
        val knjiga_naslov: String = "",
        val knjiga_cena: Float = 0.0f,
        val knjiga_leto: String = "",
        val knjiga_opis: String = ""
): Serializable