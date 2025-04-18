<?php

# Version 1.0.0

$lang['predix'] = 'PrediX';
$lang['predix_chat'] = 'Chat';
$lang['predix_image_gen'] = 'Générateur d\'images';
$lang['predix_transcription'] = 'Transcription audio';
$lang['predix_translation'] = 'Traduction audio';
$lang['predix_settings'] = 'Paramètres';
$lang['predix_generate_image_label'] = 'Générer une image avec l\'entrée fournie';
$lang['predix_generate'] = 'Générer';
$lang['predix_generated_images'] = 'Images générées';
$lang['predix_generated_images_question'] = 'Donnez aux utilisateurs la possibilité de sélectionner la taille des images qu\'ils peuvent générer, en leur donnant le contrôle sur le processus.';
$lang['predix_generate_image_number'] = 'Nombre d\'images à générer';
$lang['predix_generate_image_number_helper'] = 'Vous pouvez contrôler le nombre maximum d\'images qu\'un utilisateur peut générer lors d\'une seule session.';
$lang['predix_generate_image_size'] = 'Taille des images à générer';
$lang['predix_settings_open_ai_key'] = 'Clé secrète OpenAI - <a href="https://platform.openai.com/account/api-keys" target="_blank">OpenAI API</a>';
$lang['predix_settings_text_limit'] = 'Limite de texte du chat';
$lang['predix_settings_text_limit_question'] = 'Pour optimiser les coûts d\'ation, envisagez de limiter le nombre de mots des réponses du chat d\'OpenAI.';
$lang['predix_settings_chat_model'] = 'Modèle de chat';
$lang['predix_settings_notification'] = 'La plupart des limitations sur ces paramètres ne s\'appliquent pas aux administrateurs.';
$lang['predix_settings_predix_audio_transcription_model'] = 'Modèle de transcription audio';
$lang['predix_settings_predix_audio_translation_model'] = 'Modèle de traduction audio';
$lang['predix_settings_predix_audio_translation_model_max_filesize'] = 'Taille maximale du fichier de traduction audio en Ko';
$lang['predix_settings_predix_audio_translation_model_allowed_extensions'] = 'Extensions autorisées pour la traduction audio';
$lang['predix_settings_predix_audio_transcription_model_max_filesize'] = 'Taille maximale du fichier de transcription audio en Ko';
$lang['predix_settings_predix_audio_transcription_model_allow'] = 'Taille maximale du fichier de transcription audio en Ko';
$lang['predix_settings_predix_audio_transcription_model_allowed_extensions'] = 'Extensions autorisées pour la transcription audio';
$lang['predix_audio_translation_file_maximum_size'] = 'Le fichier audio n\'a pas pu être joint car il dépasse la taille maximale autorisée. Veuillez ajuster la taille du fichier dans les paramètres de PrediX.';
$lang['predix_audio_translation_file_extensions_err'] = 'Le fichier audio que vous essayez de télécharger n\'a pas une extension prise en charge. Veuillez ajuster les extensions autorisées dans les paramètres de PrediX.';
$lang['predix_generated_audio_translations'] = 'Traductions audio générées';
$lang['predix_generated_translated_text'] = 'Texte traduit';
$lang['predix_generated_transcribed_text'] = 'Texte transcrit';
$lang['predix_translate_button'] = 'Traduire l\'audio en anglais';
$lang['predix_upload_audio_file'] = 'Télécharger un fichier audio';
$lang['predix_select_file_to_upload'] = 'Sélectionner un fichier à télécharger';
$lang['predix_transcript_button'] = 'Transcrire';
$lang['predix_generated_audio_transcriptions'] = 'Transcriptions audio générées';
$lang['predix_chat_notification'] = 'Veuillez configurer la clé API OpenAI correcte pour utiliser les services PrediX, Paramètres de PrediX->Clé OpenAI.';
$lang['predix_delete_chat_history'] = 'Supprimer l\'historique de chat';
$lang['predix_enter_your_message'] = 'Envoyer un message...';
$lang['predix_send_message'] = 'Envoyer';
$lang['predix_settings_use_streams'] = 'Utiliser les flux PHP pour le chat';
$lang['predix_settings_use_streams_tooltip'] = "Parfois, certains serveurs rencontrent des problèmes avec les flux PHP, ce qui entraîne un dysfonctionnement du chat. Si c'est le cas, veuillez cocher NON.";
$lang['predix_template_categories'] = 'Catégories de modèles';
$lang['predix_templates'] = 'Modèles';
$lang['predix_total_template_category_templates'] = 'Total des modèles : %s';
$lang['predix_category_name'] = 'Nom de la catégorie';
$lang['predix_category_description'] = 'Description de la catégorie';
$lang['predix_is_enabled'] = 'Est activé';
$lang['predix_add_template_category'] = 'Créer une catégorie de modèle';
$lang['predix_document_name'] = 'Nom du document';
$lang['predix_document_description'] = 'Description du document';
$lang['predix_documents'] = 'Documents';
$lang['predix_template_category_failed_to_create'] = 'Échec de la création de la catégorie de modèle';
$lang['predix_template_category_failed_to_update'] = 'Échec de la mise à jour de la catégorie de modèle';
$lang['predix_create_template'] = 'Créer un modèle';
$lang['predix_input_template_name'] = 'Nom du modèle';
$lang['predix_input_template_category'] = 'Catégorie du modèle';
$lang['predix_input_template_description'] = 'Description du modèle';
$lang['predix_input_template_custom_prompt'] = 'Invite personnalisée';
$lang['predix_input_template_icon'] = 'Icône du modèle (fontawesome.com)';
$lang['predix_input_language_to_use'] = 'Langue de la réponse';
$lang['predix_input_creativity'] = 'Créativité';
$lang['predix_input_tone_of_voice'] = 'Tonalité de la voix';
$lang['predix_input_max_result_length'] = 'Longueur maximale du résultat';
$lang['predix_generate_template_text'] = 'Générer le texte';
$lang['predix_save_as_document'] = 'Enregistrer en tant que document';
$lang['predix_use_template'] = 'Utiliser le modèle';
$lang['predix_settings_reply_agent_name'] = 'Nom du bot de réponse aux tickets';
$lang['predix_settings_reply_agent_title'] = 'Titre du bot de réponse aux tickets';
$lang['predix_settings_ticket_reply_agent'] = 'Activer la réponse automatique du bot aux tickets';
$lang['predix_settings_ticket_assigned_staff'] = 'Attribuer chaque réponse du bot au personnel :';
$lang['predix_settings_ticket_reply_agent_tooltip'] = "Si cette option est activée, chaque fois qu'un client ouvre un ticket, le bot créera une réponse avec des solutions possibles et demandera au client s'il a besoin de plus d'informations.";
$lang['predix_custom_input_name'] = 'Nom de l\'entrée personnalisée';
$lang['predix_custom_input_label'] = 'Libellé de l\'entrée personnalisée';
$lang['predix_custom_input_field_type'] = 'Type de champ d\'entrée personnalisée';
$lang['predix_custom_prompt_hint'] = 'Utilisez <b>Nom de l\'entrée personnalisée</b> dans <strong>Invite personnalisée</strong> comme un champ de fusion. Par exemple, John pense à <b>{{user_thought}}</b>';

?>