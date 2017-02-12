/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package brokerrecon;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileWriter;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.Statement;
import java.text.DecimalFormat;
import java.text.SimpleDateFormat;
import java.util.Properties;
import org.apache.commons.lang3.StringUtils;
import org.apache.commons.io.*;
import org.apache.commons.mail.*;
import javax.mail.*;

/**
 *
 * @author restevez
 */
public class BrokerRecon {
    
    
    
    
    public String getDate(String lastrunfile){
    
        File file=new File(lastrunfile);
        String lastdate= "";
        
        if(file.exists()){
            System.out.println("Existe Archivo Anterior");
            try{lastdate=FileUtils.readFileToString(file);
            }catch (Exception e){System.out.println(e);}
        }else{
        
            System.out.println("No Existe Archivo Anterior");
            java.util.Date date1= new java.util.Date(System.currentTimeMillis()-86400000);
            SimpleDateFormat ft2 = new SimpleDateFormat("dd-MM-yyyy");
            lastdate= ft2.format(date1);
        }
        
    
        System.out.println(lastdate);
        return lastdate;
    }
    
    public String setDate(String lastrunfile, String currentdate){
    
        File file=new File(lastrunfile);
        
            try{
                FileUtils.writeStringToFile(file, currentdate);
            }catch (Exception e){System.out.println(e);}
        
        System.out.println("Colocar fecha de ultima corrida:"+currentdate);
        return "TRUE";
    }
    
    public boolean setFile(String myfile, String header, String recordlist, String summary){
        
        boolean result=false;
        //=====Creacion Archivo======================================
        try{
        
        File file=new File(myfile);
        FileWriter fstream=new FileWriter(myfile); 
        //============================================================
        System.out.println("Creando Archivo: "+myfile);
        
        fstream.write(header);
        fstream.write(recordlist);
        fstream.write(summary);
        fstream.close();

        
        result=true;
       
        
        }catch (Exception e){
            System.out.println(e);
            result=false;
            System.exit(1);
                    

        }
        
        
        return result;
    }
    
    
   public boolean sendEmail(String myfile,String filename,String smtp,String from,String PARTNER_NAME,String to,String cc,String cc2,String bcc, String bcc2){
       
       boolean result=false;
       try{
            EmailAttachment attachment = new EmailAttachment();
            attachment.setPath(myfile);
            attachment.setDisposition(EmailAttachment.ATTACHMENT);
            attachment.setDescription(filename);
            //attachment.setName("John");
            MultiPartEmail email=new MultiPartEmail();
            email.setHostName(smtp);
            email.setSmtpPort(25);
            email.setFrom(from);
            email.setSubject("Settlement File "+PARTNER_NAME);
            email.setMsg("Attached Settlement File Partner: "+ PARTNER_NAME);
            email.addTo(to);
            if(cc != null) email.addCc(cc);
            if(cc2 != null) email.addCc(cc2);
            if(bcc != null) email.addCc(bcc);
            if(bcc2 != null) email.addCc(bcc2);
            email.attach(attachment);
            email.send();
            System.out.println("Enviando eMail ...");
            result = true;
       }catch (Exception e){
            System.out.println(e);
            result = false;
       }
       return result;
       
   } 
    

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        // TODO code application logic here
        
        //=========Campos Fijos ================================
        String header = "";
        String record = "";
        String recordlist="";
        String summary="";
        String TRANSACTION_ID = "";
        String PARTNER_ID="";
        String PARTNER_NAME="";
        String CUSTOMER_IDENTIFIER=""; 
        String TRANSACTION_DATE="";
        String CURRENCY="";
        Double AMOUNT=0.00;
        Double PAYMENT_BROKER_TX_FEE=0.00;
        Double ITBIS=0.00;
        String lastrundate="";
        Double totalamount=0.00;
        Double totalcomission=0.00;
        Double totaltax=0.00;
        DecimalFormat df=new DecimalFormat("#.00");
        BrokerRecon tmp=new BrokerRecon();
        int i=0;
        
        //---------Formato para obtener Fecha de Corrida ----------
        java.util.Date date1= new java.util.Date();
        SimpleDateFormat ft = new SimpleDateFormat("ddMMyyyy");
        String date2= ft.format(date1);
        
        SimpleDateFormat ft2 = new SimpleDateFormat("dd-MM-yyyy");
        String currentdate= ft2.format(date1);
        
        SimpleDateFormat ft3 = new SimpleDateFormat("MMddyyyy");
        String headerdate= ft3.format(date1);
        
        //-------------------------------------------------
        System.out.println(date2);
        
        
        
        //============================================================
        //====Leer archivo de Configuracion ==========================
        Properties prop = new Properties();
        String con ="";
        String user="";
        String pass="";
        String dbnameprefix="";
        String outfilename="";
        String lastrunfile="";
        Double tax=0.00;
        String location="";
        String smtp="";
        String from="";
        String to="";
        String cc="";
        String cc2="";
        String bcc="";
        String bcc2="";
        String query = "";
        try{
            String path=args[0];
            //prop.load(new FileInputStream("conf/config.properties"));
            prop.load(new FileInputStream(path));
            con=prop.getProperty("url");
            user=prop.getProperty("username");
            pass=prop.getProperty("password");
            dbnameprefix=prop.getProperty("dbnameprefix");
            outfilename=prop.getProperty("outfilename");
            lastrunfile=prop.getProperty("partner_lastrun_filename");
            PARTNER_NAME=prop.getProperty("partner_name");
            tax=Double.valueOf(prop.getProperty("tax"));
            location=prop.getProperty("location");
            smtp=prop.getProperty("smtp");
            from=prop.getProperty("from");
            to=prop.getProperty("to");
            cc=prop.getProperty("cc");
            cc2=prop.getProperty("cc2");
            bcc=prop.getProperty("bcc");
            bcc2=prop.getProperty("bcc2");
            System.out.println(PARTNER_NAME);
            System.out.println(outfilename);
            System.out.println(to);
            lastrundate=tmp.getDate(lastrunfile);
            /*
            Query Example ================================================
            Select TRANSACTION_ID,PARTNER_ID,CUSTOMER_IDENTIFIER,
                   to_char(TRANSACTION_DATE,'MMDDYYYY') TRANSACTION_DATE,
                   CURRENCY,AMOUNT,(GCS_SERVICE_FEE*0.275) PAYMENT_BROKER_TX_FEE 
            from %s 
            where PARTNER_NAME='%s' 
              and TRANSACTION_DATE between to_date('%s 12:00:00 PM','DD-MM-YYYY HH:MI:SS PM') 
                                       and to_date('%s 12:00:00 PM','DD-MM-YYYY HH:MI:SS PM')
            */
            query=String.format(prop.getProperty("partner_query_report"),dbnameprefix,PARTNER_NAME,lastrundate,currentdate);
        }catch (Exception e){
            System.out.println(e);
        }
        //===========================================================
        //System.out.println(con);
        try{
            Class.forName("oracle.jdbc.driver.OracleDriver");
            Connection conn = DriverManager.getConnection(con, user, pass);
            Statement stmt = conn.createStatement();
            //System.out.println(query);
            ResultSet rset = stmt.executeQuery(query);
            while(rset.next()){
                TRANSACTION_ID=rset.getString("TRANSACTION_ID");
                PARTNER_ID=rset.getString("PARTNER_ID");
                CUSTOMER_IDENTIFIER=rset.getString("CUSTOMER_IDENTIFIER");
                TRANSACTION_DATE=rset.getString("TRANSACTION_DATE");
                CURRENCY=rset.getString("CURRENCY");
                AMOUNT=rset.getDouble("AMOUNT");    
                PAYMENT_BROKER_TX_FEE=rset.getDouble("PAYMENT_BROKER_TX_FEE");    
                ITBIS=PAYMENT_BROKER_TX_FEE*tax;
                record="";
                //Formatear Campos
                String transactionid=StringUtils.leftPad(String.valueOf(TRANSACTION_ID), 22, "0");
                String customerid=StringUtils.leftPad(String.valueOf(CUSTOMER_IDENTIFIER), 15, " ");
                String amount=StringUtils.leftPad(String.valueOf(df.format(AMOUNT)), 10, "0");
                String paymentbrokertxfee=StringUtils.leftPad(String.valueOf(df.format(PAYMENT_BROKER_TX_FEE)), 10, "0");
                String itbis=StringUtils.leftPad(String.valueOf(df.format(ITBIS)), 10, "0");
                //Calcula Totales
                totalamount=AMOUNT+totalamount;
                totalcomission=PAYMENT_BROKER_TX_FEE+totalcomission;
                totaltax=ITBIS+totaltax;
                //Armar Record
                record="D"+transactionid+PARTNER_ID+customerid+TRANSACTION_DATE+CURRENCY+amount+paymentbrokertxfee+itbis;
                recordlist=recordlist+record+"\n";        
                //System.out.println(record);
                i++;
            }
         
            String totalcount=StringUtils.leftPad(String.valueOf(i), 8, "0");
            String total=StringUtils.leftPad(String.valueOf(df.format(totalamount)), 12, "0");
            String comission=StringUtils.leftPad(String.valueOf(df.format(totalcomission)), 12, "0");
            String totalta = StringUtils.leftPad(String.valueOf(df.format(totaltax)), 12, "0");
            header="H"+"GCSBP"+headerdate+"\n";
            summary="T"+totalcount+total+comission+totalta;
            System.out.println(header);
            System.out.println(recordlist);
            System.out.println(summary);
            rset.close();
            stmt.close();
            conn.close();
        }catch (Exception e){
            System.out.println(e);
            System.exit(1);
        }
        
        BrokerRecon generator=new BrokerRecon();
        String myfile=location+outfilename+"_"+date2+".txt";
        generator.setFile(myfile,header,recordlist,summary);
        generator.setDate(lastrunfile,currentdate);
        String filename=outfilename+"_"+PARTNER_NAME+"_"+date2+".txt";
        generator.sendEmail(myfile,filename,smtp,from,PARTNER_NAME,to,cc,cc2,bcc,bcc2);
        System.out.println("Proceso Completado Exitosamente !!!");
        System.exit(0);
    }
}
