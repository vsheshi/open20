SPOOL 001_psicheweb_V_DI_ESPORTA_LOG_AN_create_view.log
SET DEFINE OFF; 
SET TIMING ON; 
SET TIME ON; 
SET ECHO ON; 
SET SQLBL ON; 

CREATE OR REPLACE FORCE VIEW V_DI_ESPORTA_LOG_AN (ID_ESPORTA_LOG, DT_INIZIO_VALIDITA, ID_UOP, DT_DAL, DT_AL, FL_ANNULLA, CD_USERID) AS 
SELECT   el.id_esporta_log, el.dt_inizio_validita, el.id_uop, el.dt_dal,
		el.dt_al, el.fl_annulla, el.cd_userid
   FROM di_esporta_log_an el, 
		(SELECT   MAX (AN.dt_inizio_validita) dt_inizio_validita,
				  AN.id_esporta_log
			 FROM di_esporta_log_an AN, V_PS_DI_AN V
	WHERE AN.ID_UOP = V.ID_UOP AND AN.DT_INIZIO_VALIDITA = V.dt_inizio_validita
		 GROUP BY id_esporta_log) el2
  WHERE el.id_esporta_log = el2.id_esporta_log
	AND el.dt_inizio_validita = el2.dt_inizio_validita
ORDER BY el.id_esporta_log;

/

SHOW ERRORS;   
SPOOL OFF;
